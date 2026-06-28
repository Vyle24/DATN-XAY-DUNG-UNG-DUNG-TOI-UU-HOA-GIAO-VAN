<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipper;
use App\Models\TrackingLog;
use App\Models\ShipperAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShipperController extends Controller
{
    /**
     * Display the adaptive dashboard for the logged-in shipper.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return redirect()->route('login')->with('error', 'Tài khoản chưa có thông tin Shipper.');
        }

        // Get assigned Routes waiting for confirmation
        $assignedRoutes = \App\Models\Route::where('shipper_id', $shipper->id)
            ->whereHas('routeOrders.order', function ($query) {
                $query->where('status', 'pending');
            })
            ->with(['routeOrders.order'])
            ->latest('id')
            ->get();

        // Get single assigned orders waiting for confirmation (not part of any route)
        $assignedSingleOrders = Order::where('shipper_id', $shipper->id)
            ->where('status', 'pending')
            ->where('assignment_status', 'assigned')
            ->whereDoesntHave('routeOrders')
            ->with(['customer.user', 'hub'])
            ->orderBy('id', 'desc')
            ->get();

        // Get all active orders currently being delivered
        $deliveringOrders = Order::where('shipper_id', $shipper->id)
            ->where('status', 'processing')
            ->with(['customer.user', 'hub'])
            ->orderBy('id', 'desc')
            ->get();

        // Get delivery history (status is 'delivered' or 'failed')
        $query = Order::where('shipper_id', $shipper->id)
            ->whereIn('status', ['delivered', 'failed'])
            ->with(['customer.user', 'hub']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('customer.user', function($qu) use ($search) {
                      $qu->where('username', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        $historyOrders = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        // If AJAX request (Lazy Load)
        if ($request->ajax()) {
            $desktopHtml = view('shipper.partials.history_list_desktop', compact('historyOrders'))->render();
            $mobileHtml = view('shipper.partials.history_list_mobile', compact('historyOrders'))->render();
            return response()->json([
                'desktop_html' => $desktopHtml,
                'mobile_html' => $mobileHtml,
                'hasMorePages' => $historyOrders->hasMorePages()
            ]);
        }

        // Retrieve the active route for this shipper (containing pending or processing orders)
        $activeRoute = \App\Models\Route::where('shipper_id', $shipper->id)
            ->whereHas('routeOrders.order', function ($query) {
                $query->where('status', 'processing');
            })
            ->with(['routeOrders.order.customer.user', 'routeOrders.order.hub'])
            ->latest('id')
            ->first();

        // Calculate statistics
        $stats = [
            'total_delivered' => Order::where('shipper_id', $shipper->id)->where('status', 'delivered')->count(),
            'total_processing' => $deliveringOrders->count(),
            'total_assigned' => $assignedSingleOrders->count() + ($assignedRoutes->sum(function($r) { return $r->routeOrders->count(); })),
            'wallet_balance' => $shipper->wallet_balance,
        ];

        return view('shipper.dashboard', compact('assignedRoutes', 'assignedSingleOrders', 'deliveringOrders', 'historyOrders', 'shipper', 'stats', 'activeRoute'));
    }

    /**
     * Update order status to 'delivered' (completing delivery and adding fee to wallet).
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return back()->with('error', 'Không tìm thấy hồ sơ Shipper.');
        }

        $order = Order::where('id', $id)
            ->where('shipper_id', $shipper->id)
            ->firstOrFail();

        if ($order->status === 'delivered') {
            return back()->with('info', 'Đơn hàng này đã được xác nhận giao thành công trước đó.');
        }

        DB::transaction(function () use ($order, $shipper) {
            // Update order status & assignment_status
            $order->status            = 'delivered';
            $order->assignment_status = 'completed';
            $order->save();

            // Credit shipper wallet with shipping fee
            $shipper->wallet_balance += $order->shipping_fee;
            $shipper->save();

            // Create tracking log
            TrackingLog::create([
                'order_id'       => $order->id,
                'location_name'  => $order->delivery_address,
                'status_at_time' => 'delivered',
                'lat'            => $order->delivery_lat,
                'lng'            => $order->delivery_lng,
            ]);

            // Update or create ShipperAssignment record
            ShipperAssignment::updateOrCreate(
                ['order_id' => $order->id, 'shipper_id' => $shipper->id],
                ['status' => 'accepted', 'responded_at' => now()]
            );
        });

        // Send Notification to Customer
        if ($order->customer && $order->customer->user) {
            $order->customer->user->notify(new \App\Notifications\OrderStatusChangedNotification($order));
        }

        return back()
            ->with('success', "Đã hoàn thành giao hàng! Cước phí " . number_format($order->shipping_fee) . "đ đã được cộng vào ví của bạn.");
    }

    /**
     * Accept assigned Route (all orders within it).
     */
    public function acceptRoute($id)
    {
        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return back()->with('error', 'Không tìm thấy hồ sơ Shipper.');
        }

        $route = \App\Models\Route::where('id', $id)
            ->where('shipper_id', $shipper->id)
            ->firstOrFail();

        DB::transaction(function () use ($route, $shipper) {
            $routeOrders = \App\Models\RouteOrder::where('route_id', $route->id)->with('order')->get();
            
            foreach ($routeOrders as $ro) {
                $order = $ro->order;
                if ($order && $order->status === 'pending') {
                    $order->status = 'processing';
                    $order->save();

                    \App\Models\TrackingLog::create([
                        'order_id'       => $order->id,
                        'location_name'  => $order->pickup_address,
                        'status_at_time' => 'processing',
                        'lat'            => $order->pickup_lat,
                        'lng'            => $order->pickup_lng,
                    ]);

                    \App\Models\ShipperAssignment::updateOrCreate(
                        ['order_id' => $order->id, 'shipper_id' => $shipper->id],
                        ['status' => 'accepted', 'responded_at' => now()]
                    );

                    // Send Notification to Customer
                    if ($order->customer && $order->customer->user) {
                        $order->customer->user->notify(new \App\Notifications\OrderStatusChangedNotification($order));
                    }
                }
            }
        });

        return redirect()->route('shipper.dashboard', ['tab' => 'route'])
            ->with('success', 'Đã nhận toàn bộ tuyến đường! Vui lòng thao tác trên Bản đồ Lộ trình.');
    }

    /**
     * Accept assigned order.
     */
    public function acceptOrder($id)
    {
        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return back()->with('error', 'Không tìm thấy hồ sơ Shipper.');
        }

        $order = Order::where('id', $id)
            ->where('shipper_id', $shipper->id)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::transaction(function () use ($order, $shipper) {
            $order->status = 'processing';
            $order->save();

            // Create tracking log
            TrackingLog::create([
                'order_id'       => $order->id,
                'location_name'  => $order->pickup_address,
                'status_at_time' => 'processing',
                'lat'            => $order->pickup_lat,
                'lng'            => $order->pickup_lng,
            ]);

            // Update or create ShipperAssignment record
            ShipperAssignment::updateOrCreate(
                ['order_id' => $order->id, 'shipper_id' => $shipper->id],
                ['status' => 'accepted', 'responded_at' => now()]
            );

            // Check if this order belongs to a Route. If so, accept ALL orders in the same route automatically.
            $routeOrder = \App\Models\RouteOrder::where('order_id', $order->id)->first();
            if ($routeOrder) {
                $otherOrderIds = \App\Models\RouteOrder::where('route_id', $routeOrder->route_id)
                    ->where('order_id', '!=', $order->id)
                    ->pluck('order_id');
                
                if ($otherOrderIds->isNotEmpty()) {
                    Order::whereIn('id', $otherOrderIds)
                        ->where('status', 'pending')
                        ->update(['status' => 'processing']);

                    foreach ($otherOrderIds as $oid) {
                        $otherOrder = Order::find($oid);
                        if ($otherOrder && $otherOrder->status === 'processing') {
                            TrackingLog::create([
                                'order_id'       => $otherOrder->id,
                                'location_name'  => $otherOrder->pickup_address,
                                'status_at_time' => 'processing',
                                'lat'            => $otherOrder->pickup_lat,
                                'lng'            => $otherOrder->pickup_lng,
                            ]);
                            
                            ShipperAssignment::updateOrCreate(
                                ['order_id' => $otherOrder->id, 'shipper_id' => $shipper->id],
                                ['status' => 'accepted', 'responded_at' => now()]
                            );
                        }
                    }
                }
            }
        });

        // Send Notification to Customer
        if ($order->customer && $order->customer->user) {
            $order->customer->user->notify(new \App\Notifications\OrderStatusChangedNotification($order));
        }

        return back()
            ->with('success', "Đã nhận đơn hàng {$order->order_code}! Vui lòng tiến hành lấy hàng và giao.");
    }

    /**
     * Decline assigned order.
     */
    public function declineOrder($id)
    {
        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return back()->with('error', 'Không tìm thấy hồ sơ Shipper.');
        }

        $order = Order::where('id', $id)
            ->where('shipper_id', $shipper->id)
            ->where('status', 'pending')
            ->firstOrFail();

        DB::transaction(function () use ($order, $shipper) {
            // Unassign order from shipper
            $order->shipper_id        = null;
            $order->assignment_status = 'unassigned';
            $order->save();

            // Remove from route_orders if this order was in a route
            \App\Models\RouteOrder::where('order_id', $order->id)->delete();

            // Create tracking log
            TrackingLog::create([
                'order_id'       => $order->id,
                'location_name'  => 'Shipper từ chối đơn hàng',
                'status_at_time' => 'unassigned',
                'lat'            => $order->pickup_lat,
                'lng'            => $order->pickup_lng,
            ]);

            // Mark assignment as rejected
            ShipperAssignment::updateOrCreate(
                ['order_id' => $order->id, 'shipper_id' => $shipper->id],
                ['status' => 'rejected', 'responded_at' => now()]
            );
        });

        return back()
            ->with('success', "Đã từ chối đơn hàng {$order->order_code}.");
    }

    /**
     * Report order delivery failure.
     */
    public function failOrder(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return back()->with('error', 'Không tìm thấy hồ sơ Shipper.');
        }

        $order = Order::where('id', $id)
            ->where('shipper_id', $shipper->id)
            ->where('status', 'processing')
            ->firstOrFail();

        DB::transaction(function () use ($order, $request) {
            $order->status = 'failed';
            $order->assignment_status = 'failed_delivery';
            $order->save();

            // Create tracking log
            TrackingLog::create([
                'order_id' => $order->id,
                'location_name' => 'Thất bại: ' . $request->reason,
                'status_at_time' => 'failed',
                'lat' => $order->delivery_lat,
                'lng' => $order->delivery_lng,
            ]);
        });

        // Send Notification to Customer
        if ($order->customer && $order->customer->user) {
            $order->customer->user->notify(new \App\Notifications\OrderStatusChangedNotification($order));
        }

        return back()
            ->with('success', "Đã báo cáo giao hàng thất bại cho đơn hàng {$order->order_code}.");
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus()
    {
        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return back()->with('error', 'Không tìm thấy hồ sơ Shipper.');
        }

        $shipper->is_active = !$shipper->is_active;
        $shipper->save();

        $statusStr = $shipper->is_active ? 'Trực tuyến (Online)' : 'Ngoại tuyến (Offline)';
        return back()->with('success', "Đã cập nhật trạng thái làm việc: {$statusStr}");
    }

    /**
     * Update shipper profile info (phone, vehicle_type, license_no).
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone'    => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
            'region'   => 'nullable|string|max:50',
        ]);

        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return back()->with('error', 'Không tìm thấy hồ sơ Shipper.');
        }

        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        if ($request->filled('region')) {
            $shipper->region = $request->region;
            $shipper->save();
        }

        if ($user->isDirty()) {
            $user->save();
        }

        return back()->with('success', 'Đã cập nhật thông tin cá nhân thành công!');
    }

    /**
     * Show Shipper Profile page.
     */
    public function profile()
    {
        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return redirect()->route('login')->with('error', 'Không tìm thấy hồ sơ Shipper.');
        }

        // Delivery statistics
        $totalDelivered = Order::where('shipper_id', $shipper->id)->where('status', 'delivered')->count();
        $totalFailed    = Order::where('shipper_id', $shipper->id)->where('status', 'failed')->count();
        $totalEarnings  = Order::where('shipper_id', $shipper->id)->where('status', 'delivered')->sum('shipping_fee');

        return view('shipper.profile', compact('user', 'shipper', 'totalDelivered', 'totalFailed', 'totalEarnings'));
    }

    /**
     * Show Shipper Earnings History.
     */
    public function earnings(Request $request)
    {
        $user = Auth::user();
        $shipper = $user->shipper;

        if (!$shipper) {
            return redirect()->route('login')->with('error', 'Không tìm thấy hồ sơ Shipper.');
        }

        $query = Order::where('shipper_id', $shipper->id)
            ->where('status', 'delivered')
            ->with(['customer.user', 'hub']);

        if ($request->filled('date_from')) {
            $query->whereDate('updated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('updated_at', '<=', $request->date_to);
        }

        // Get delivered orders (earnings history)
        $deliveredOrders = $query->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();

        // Stats calculation needs to clone the query before pagination
        $statsQuery = Order::where('shipper_id', $shipper->id)->where('status', 'delivered');
        if ($request->filled('date_from')) {
            $statsQuery->whereDate('updated_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $statsQuery->whereDate('updated_at', '<=', $request->date_to);
        }

        $totalEarnings  = $statsQuery->sum('shipping_fee');
        $totalDelivered = $statsQuery->count();
        $avgEarning     = $totalDelivered > 0 ? $totalEarnings / $totalDelivered : 0;

        $stats = [
            'total_earnings'  => $totalEarnings,
            'total_delivered' => $totalDelivered,
            'avg_per_order'   => round($avgEarning),
            'wallet_balance'  => $shipper->wallet_balance,
        ];

        return view('shipper.earnings', compact('shipper', 'deliveredOrders', 'stats'));
    }
}
