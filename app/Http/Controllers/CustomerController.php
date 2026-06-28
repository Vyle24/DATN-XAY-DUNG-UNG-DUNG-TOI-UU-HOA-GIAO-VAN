<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Hub;
use App\Models\Voucher;
use App\Models\DistrictShippingRate;
use App\Models\TrackingLog;
use App\Models\Customer;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Helper to get current authenticated Customer profile.
     */
    private function getCustomerProfile()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        
        // Find or create customer entry just in case
        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            $customer = Customer::create([
                'user_id' => $user->id,
                'membership_level' => 'Standard',
                'points' => 0
            ]);
        }
        return $customer;
    }

    /**
     * Display Customer Dashboard.
     */
    public function dashboard(Request $request)
    {
        $customer = $this->getCustomerProfile();
        if (!$customer) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Không tìm thấy hồ sơ khách hàng'], 404);
            }
            return redirect()->route('login')->with('error', 'Không tìm thấy hồ sơ khách hàng.');
        }

        // Query setup
        $query = Order::where('customer_id', $customer->id)
            ->with(['shipper.user', 'hub']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('order_code', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Paginator for Lazy load
        $recentOrders = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // If AJAX request (Lazy Load)
        if ($request->ajax()) {
            $html = view('customer.partials.order_list', compact('recentOrders'))->render();
            return response()->json([
                'html' => $html,
                'hasMorePages' => $recentOrders->hasMorePages()
            ]);
        }

        // Stats calculation
        $allOrders = Order::where('customer_id', $customer->id)->get();
        
        $totalOrders = $allOrders->count();
        $deliveredOrders = $allOrders->where('status', 'delivered')->count();
        $failedOrders = $allOrders->where('status', 'failed')->count();
        $totalSpent = $allOrders->where('status', 'delivered')->sum('shipping_fee');

        $successRate = $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100) : 0;

        $stats = [
            'total' => $totalOrders,
            'pending' => $allOrders->where('status', 'pending')->count(),
            'processing' => $allOrders->where('status', 'processing')->count(),
            'delivered' => $deliveredOrders,
            'failed' => $failedOrders,
            'total_spent' => $totalSpent,
            'success_rate' => $successRate
        ];

        return view('customer.dashboard', compact('customer', 'recentOrders', 'stats'));
    }

    /**
     * Create Order Page.
     */
    public function createOrder()
    {
        $customer = $this->getCustomerProfile();
        $hubs = Hub::whereIn('city', ['hanoi', 'hcm'])->get();
        $vouchers = Voucher::where(function($q) {
            $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()->toDateString());
        })->get();
        $rates = DistrictShippingRate::whereIn('city', ['hanoi', 'hcm'])->orderBy('district_name', 'asc')->get();

        // Get global pricing settings, default if not set
        $baseWeightLimit = (float) (SystemSetting::where('key', 'base_weight_limit')->value('value') ?? 2.0);
        $pricePerKg = (float) (SystemSetting::where('key', 'price_per_kg')->value('value') ?? 5000.0);

        return view('customer.create_order', compact('customer', 'hubs', 'vouchers', 'rates', 'baseWeightLimit', 'pricePerKg'));
    }

    /**
     * Helper to compute Haversine distance.
     */
    private function getDistance($lat1, $lon1, $lat2, $lon2)
    {
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $R * $c;
    }

    /**
     * Store Order in Database.
     */
    public function storeOrder(Request $request)
    {
        $customer = $this->getCustomerProfile();
        if (!$customer) {
            return back()->with('error', 'Không tìm thấy hồ sơ khách hàng.');
        }

        $request->validate([
            'hub_id' => 'required|exists:hubs,id',
            'pickup_address' => 'required|string|max:255',
            'pickup_lat' => 'required|numeric',
            'pickup_lng' => 'required|numeric',
            'delivery_address' => 'required|string|max:255',
            'delivery_lat' => 'required|numeric',
            'delivery_lng' => 'required|numeric',
            'total_weight' => 'required|numeric|min:0.01|max:1000',
            'payment_method' => 'required|string|in:COD,Prepaid',
            'voucher_code' => 'nullable|string|exists:vouchers,code',
            'district_rate_id' => 'required|exists:districts_shipping_rates,id',
        ]);

        // Recalculate shipping fee in Backend for safety
        $hub = Hub::findOrFail($request->hub_id);
        $rate = DistrictShippingRate::findOrFail($request->district_rate_id);
        
        $distance = $this->getDistance(
            (float) $hub->latitude, (float) $hub->longitude,
            (float) $request->delivery_lat, (float) $request->delivery_lng
        );

        $baseRate = (float) $rate->base_price;
        $pricePerKm = (float) $rate->price_per_km;

        $subtotal = $baseRate + ($distance * $pricePerKm);

        // Add weight pricing
        $baseWeightLimit = (float) (SystemSetting::where('key', 'base_weight_limit')->value('value') ?? 2.0);
        $pricePerKg = (float) (SystemSetting::where('key', 'price_per_kg')->value('value') ?? 5000.0);

        if ($request->total_weight > $baseWeightLimit) {
            $extraWeight = $request->total_weight - $baseWeightLimit;
            $subtotal += $extraWeight * $pricePerKg;
        }

        // Apply voucher if exists
        $discount = 0;
        if ($request->filled('voucher_code')) {
            $voucher = Voucher::where('code', $request->voucher_code)->first();
            if ($voucher && (!$voucher->expiry_date || !$voucher->expiry_date->isPast())) {
                $discount = ($subtotal * $voucher->discount_percent) / 100;
                if ($voucher->max_discount > 0 && $discount > $voucher->max_discount) {
                    $discount = (float) $voucher->max_discount;
                }
            }
        }

        $finalShippingFee = max($subtotal - $discount, 0);

        // Generate unique ORD-CODE
        $orderCode = 'ORD-' . strtoupper(Str::random(8));
        while (Order::where('order_code', $orderCode)->exists()) {
            $orderCode = 'ORD-' . strtoupper(Str::random(8));
        }

        DB::transaction(function() use ($request, $customer, $orderCode, $finalShippingFee) {
            $order = Order::create([
                'order_code' => $orderCode,
                'customer_id' => $customer->id,
                'hub_id' => $request->hub_id,
                'pickup_address' => $request->pickup_address,
                'pickup_lat' => $request->pickup_lat,
                'pickup_lng' => $request->pickup_lng,
                'delivery_address' => $request->delivery_address,
                'delivery_lat' => $request->delivery_lat,
                'delivery_lng' => $request->delivery_lng,
                'total_weight' => $request->total_weight,
                'shipping_fee' => $finalShippingFee,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'assignment_status' => 'unassigned',
            ]);

            // Save order creation log
            TrackingLog::create([
                'order_id' => $order->id,
                'location_name' => $order->pickup_address,
                'status_at_time' => 'created',
                'lat' => $order->pickup_lat,
                'lng' => $order->pickup_lng,
            ]);

            // Award points for creating order
            $customer->points += 10;
            if ($customer->points >= 1000) {
                $customer->membership_level = 'Platinum';
            } elseif ($customer->points >= 500) {
                $customer->membership_level = 'Diamond';
            } elseif ($customer->points >= 200) {
                $customer->membership_level = 'Gold';
            } elseif ($customer->points >= 100) {
                $customer->membership_level = 'Silver';
            }
            $customer->save();
        });

        return redirect()->route('customer.dashboard')->with('success', 'Đơn hàng mới đã được đặt thành công! Bạn được cộng +10 điểm thưởng.');
    }

    /**
     * Real-time Order Tracking view.
     */
    public function trackOrder($id)
    {
        $customer = $this->getCustomerProfile();
        $order = Order::where('customer_id', $customer->id)
            ->with(['shipper.user', 'hub', 'trackingLogs'])
            ->findOrFail($id);

        return view('customer.track_order', compact('customer', 'order'));
    }

    /**
     * List all Vouchers.
     */
    public function vouchers()
    {
        $customer = $this->getCustomerProfile();
        $vouchers = Voucher::orderBy('id', 'desc')->get();
        return view('customer.vouchers', compact('customer', 'vouchers'));
    }

    /**
     * API: Verify Voucher Code via AJAX.
     */
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $voucher = Voucher::where('code', $request->code)->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn.'
            ], 422);
        }

        if ($voucher->expiry_date && $voucher->expiry_date->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá này đã quá hạn sử dụng.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'code' => $voucher->code,
            'discount_percent' => $voucher->discount_percent,
            'max_discount' => (float) $voucher->max_discount,
            'message' => "Áp dụng mã giảm giá thành công! Giảm {$voucher->discount_percent}% (Tối đa " . number_format($voucher->max_discount) . "đ)"
        ]);
    }

    /**
     * Show Profile Settings.
     */
    public function profile()
    {
        $customer = $this->getCustomerProfile();
        $user = Auth::user();
        return view('customer.profile', compact('customer', 'user'));
    }

    /**
     * Cancel a pending order (only if still in 'pending' status and not yet assigned).
     */
    public function cancelOrder($id)
    {
        $customer = $this->getCustomerProfile();
        if (!$customer) {
            return back()->with('error', 'Không tìm thấy hồ sơ khách hàng.');
        }

        $order = Order::where('customer_id', $customer->id)
            ->where('id', $id)
            ->firstOrFail();

        // Only allow cancel if still pending and unassigned
        if ($order->status !== 'pending' || $order->assignment_status !== 'unassigned') {
            return back()->with('error', 'Chỉ có thể hủy đơn hàng chưa được điều phối (Unassigned). Đơn hàng đã được đưa vào lịch trình hoặc điều phối cho Shipper không thể hủy.');
        }

        DB::transaction(function() use ($order, $customer) {
            $order->status = 'cancelled';
            $order->assignment_status = 'unassigned';
            $order->shipper_id = null;
            $order->save();

            \App\Models\RouteOrder::where('order_id', $order->id)->delete();

            // Log cancellation
            TrackingLog::create([
                'order_id'       => $order->id,
                'location_name'  => 'Khách hàng hủy đơn hàng',
                'status_at_time' => 'cancelled',
                'lat'            => $order->pickup_lat,
                'lng'            => $order->pickup_lng,
            ]);
        });

        return back()->with('success', "Đơn hàng {$order->order_code} đã được hủy thành công.");
    }

    /**
     * Update Profile Info.
     */
    public function updateProfile(Request $request)
    {
        $customer = $this->getCustomerProfile();
        $user = Auth::user();

        $request->validate([
            'username' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        DB::transaction(function() use ($request, $user) {
            $user->username = $request->username;
            $user->phone = $request->phone;
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }
            $user->save();
        });

        return back()->with('success', 'Cập nhật thông tin tài khoản thành công!');
    }
}
