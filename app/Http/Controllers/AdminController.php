<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\Shipper;
use App\Models\Customer;
use App\Models\Hub;
use App\Models\DistrictShippingRate;
use App\Models\Voucher;
use App\Models\Route as LogisticsRoute;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Role;

class AdminController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $processingOrders = Order::where('status', 'processing')->count();
        $activeShippersCount = Shipper::where('is_active', 1)->count();
        $orders = Order::with(['customer.user', 'shipper.user', 'hub'])->latest()->take(10)->get();
        $shippersList = Shipper::with('user')->take(5)->get();
        
        $chartData = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalOrders', 
            'processingOrders', 
            'activeShippersCount', 
            'orders', 
            'shippersList', 
            'chartData'
        ));
    }

    // --- Orders Management ---
    public function orders(Request $request)
    {
        $query = Order::with(['customer.user', 'shipper.user', 'hub']);
        
        // Cập nhật để chỉ hiển thị các order thuộc hub ở khu vực HN và HCM
        $query->whereHas('hub', function($q) {
            $q->whereIn('city', ['hanoi', 'hcm']);
        });

        if ($request->filled('search')) {
            $query->where('order_code', 'like', '%' . $request->search . '%');
        }

        $orders = $query->latest()->paginate(20);

        $stats = [
            'total' => Order::count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'total_fee' => Order::sum('shipping_fee'),
            'total_weight' => Order::sum('total_weight')
        ];

        $hubs = Hub::whereIn('city', ['hanoi', 'hcm'])->get();
        $shippers = Shipper::with('user')->get();
        $customers = Customer::with('user')->get();

        return view('admin.orders', compact('orders', 'stats', 'hubs', 'shippers', 'customers'));
    }

    public function storeOrder(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'hub_id' => 'required|exists:hubs,id',
            'shipper_id' => 'nullable|exists:shippers,id',
            'pickup_address' => 'required|string',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'delivery_address' => 'required|string',
            'delivery_lat' => 'nullable|numeric',
            'delivery_lng' => 'nullable|numeric',
            'total_weight' => 'required|numeric',
            'shipping_fee' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        
        $data['order_code'] = 'ORD-' . time();
        Order::create($data);
        return redirect()->route('admin.orders')->with('success', 'Tạo đơn hàng thành công.');
    }

    public function updateOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $data = $request->validate([
            'status' => 'required|string',
            'shipper_id' => 'nullable|exists:shippers,id',
            'hub_id' => 'required|exists:hubs,id',
            'pickup_address' => 'required|string',
            'pickup_lat' => 'nullable|numeric',
            'pickup_lng' => 'nullable|numeric',
            'delivery_address' => 'required|string',
            'delivery_lat' => 'nullable|numeric',
            'delivery_lng' => 'nullable|numeric',
            'total_weight' => 'required|numeric',
            'shipping_fee' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
        ]);

        if (array_key_exists('shipper_id', $data) && $data['shipper_id'] !== $order->shipper_id) {
            if (empty($data['shipper_id'])) {
                $data['assignment_status'] = 'unassigned';
                \App\Models\ShipperAssignment::where('order_id', $order->id)->delete();
            } else {
                $data['assignment_status'] = 'assigned';
                \App\Models\ShipperAssignment::updateOrCreate(
                    ['order_id' => $order->id, 'shipper_id' => $data['shipper_id']],
                    ['status' => 'assigned', 'responded_at' => null]
                );
            }
        }

        $order->update($data);
        return redirect()->route('admin.orders')->with('success', 'Cập nhật đơn hàng thành công.');
    }

    public function destroyOrder($id)
    {
        Order::destroy($id);
        return back()->with('success', 'Đã xoá đơn hàng.');
    }

    public function exportOrdersCsv(Request $request)
    {
        return back()->with('info', 'Tính năng export đang phát triển.');
    }

    public function printOrder($id)
    {
        $order = Order::with(['customer.user', 'shipper.user', 'hub'])->findOrFail($id);
        return view('admin.orders_print', compact('order'));
    }

    // --- Shippers Management ---
    public function shippers(Request $request)
    {
        $shippers = Shipper::with('user')->paginate(20);

        $stats = [
            'total' => Shipper::count(),
            'active' => Shipper::where('is_active', 1)->count(),
            'inactive' => Shipper::where('is_active', 0)->count(),
            'total_wallet' => Shipper::sum('wallet_balance'),
            'avg_wallet' => Shipper::avg('wallet_balance') ?? 0
        ];

        return view('admin.shippers', compact('shippers', 'stats'));
    }

    public function storeShipper(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'license_no' => 'nullable|string|max:100',
            'vehicle_type' => 'nullable|string|max:50',
            'wallet_balance' => 'nullable|numeric',
            'status' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'role_id' => 2,
                'status' => $data['status'] ?? 1,
            ]);

            Shipper::create([
                'user_id' => $user->id,
                'license_no' => $data['license_no'] ?? null,
                'vehicle_type' => $data['vehicle_type'] ?? null,
                'wallet_balance' => $data['wallet_balance'] ?? 0,
                'is_active' => $data['status'] ?? 1,
            ]);
            DB::commit();
            return back()->with('success', 'Tạo Shipper thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function updateShipper(Request $request, $id)
    {
        $shipper = Shipper::findOrFail($id);
        $user = $shipper->user;

        $data = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'license_no' => 'nullable|string|max:100',
            'vehicle_type' => 'nullable|string|max:50',
            'wallet_balance' => 'nullable|numeric',
            'status' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'status' => $data['status'] ?? 1,
            ];
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $user->update($userData);

            $shipper->update([
                'license_no' => $data['license_no'] ?? null,
                'vehicle_type' => $data['vehicle_type'] ?? null,
                'wallet_balance' => $data['wallet_balance'] ?? 0,
                'is_active' => $data['status'] ?? 1,
            ]);
            DB::commit();
            return back()->with('success', 'Cập nhật thông tin Shipper thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroyShipper($id)
    {
        Shipper::destroy($id);
        return back()->with('success', 'Đã xoá Shipper.');
    }

    public function toggleShipperStatus($id)
    {
        $shipper = Shipper::findOrFail($id);
        $shipper->is_active = !$shipper->is_active;
        $shipper->save();
        return back()->with('success', 'Đã thay đổi trạng thái Shipper.');
    }

    // --- Customers Management ---
    public function customers(Request $request)
    {
        $customers = Customer::with('user')->paginate(20);

        $stats = [
            'total' => Customer::count(),
            'vip' => Customer::where('membership_level', 'VIP')->count(),
            'gold' => Customer::where('membership_level', 'Gold')->count(),
            'silver' => Customer::where('membership_level', 'Silver')->count(),
            'total_points' => Customer::sum('points')
        ];

        return view('admin.customers', compact('customers', 'stats'));
    }

    public function storeCustomer(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'membership_level' => 'nullable|string|max:50',
            'points' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'role_id' => 3, // Customer role
                'status' => 1,
            ]);

            Customer::create([
                'user_id' => $user->id,
                'membership_level' => $data['membership_level'] ?? 'Silver',
                'points' => $data['points'] ?? 0,
            ]);
            DB::commit();
            return back()->with('success', 'Tạo Customer thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);
        $user = $customer->user;

        $data = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'membership_level' => 'nullable|string|max:50',
            'points' => 'nullable|numeric',
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
            ];
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $user->update($userData);

            $customer->update([
                'membership_level' => $data['membership_level'] ?? 'Silver',
                'points' => $data['points'] ?? 0,
            ]);
            DB::commit();
            return back()->with('success', 'Cập nhật Customer thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function destroyCustomer($id)
    {
        Customer::destroy($id);
        return back()->with('success', 'Đã xoá Customer.');
    }

    // --- Hubs Management ---
    public function hubs(Request $request)
    {
        $query = Hub::query();
        if ($request->filled('city') && in_array($request->input('city'), ['hanoi', 'hcm'])) {
            $query->where('city', $request->input('city'));
        } else {
            $query->whereIn('city', ['hanoi', 'hcm']);
        }
        
        $hubs = $query->paginate(20);

        $stats = [
            'total' => Hub::count(),
            'total_orders' => Order::whereNotNull('hub_id')->count()
        ];

        $allHubs = (clone $query)->get();

        return view('admin.hubs', compact('hubs', 'stats', 'allHubs'));
    }

    public function storeHub(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'city'      => 'required|in:hanoi,hcm',
            'address'   => 'required|string|max:255',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Hub::create($data);

        return redirect()->route('admin.hubs')->with('success', 'Điểm điều phối (Hub) mới đã được khởi tạo!');
    }

    public function updateHub(Request $request, $id)
    {
        $hub = Hub::findOrFail($id);
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'city'      => 'required|in:hanoi,hcm',
            'address'   => 'required|string|max:255',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $hub->update($data);
        return back()->with('success', 'Cập nhật Hub thành công.');
    }

    public function destroyHub($id)
    {
        Hub::destroy($id);
        return back()->with('success', 'Đã xoá Hub.');
    }

    // --- Rates Management ---
    public function rates(Request $request)
    {
        $query = DistrictShippingRate::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('district_name', 'like', "%{$search}%");
        }

        if ($request->filled('city') && in_array($request->input('city'), ['hanoi', 'hcm'])) {
            $query->where('city', $request->input('city'));
        } else {
            $query->whereIn('city', ['hanoi', 'hcm']);
        }

        $rates = $query->paginate(20);

        $stats = [
            'total' => DistrictShippingRate::count(),
            'avg_base_price' => DistrictShippingRate::avg('base_price') ?? 0,
            'avg_price_per_km' => DistrictShippingRate::avg('price_per_km') ?? 0,
            'max_price_per_km' => DistrictShippingRate::max('price_per_km') ?? 0
        ];

        return view('admin.rates', compact('rates', 'stats'));
    }

    public function storeRate(Request $request)
    {
        $data = $request->validate([
            'district_name' => 'required|string|max:255',
            'city'          => 'required|in:hanoi,hcm',
            'base_price'    => 'required|numeric|min:0',
            'price_per_km'  => 'required|numeric|min:0',
        ]);

        DistrictShippingRate::create($data);
        return back()->with('success', 'Thêm bảng giá thành công.');
    }

    public function updateRate(Request $request, $id)
    {
        $rate = DistrictShippingRate::findOrFail($id);
        $data = $request->validate([
            'district_name' => 'required|string|max:255',
            'city'          => 'required|in:hanoi,hcm',
            'base_price'    => 'required|numeric|min:0',
            'price_per_km'  => 'required|numeric|min:0',
        ]);

        $rate->update($data);
        return back()->with('success', 'Cập nhật bảng giá thành công.');
    }

    public function destroyRate($id)
    {
        DistrictShippingRate::destroy($id);
        return back()->with('success', 'Đã xoá bảng giá.');
    }

    public function updateSettings(Request $request)
    {
        return back()->with('success', 'Cập nhật cài đặt thành công.');
    }

    // --- Vouchers Management ---
    public function vouchers(Request $request)
    {
        $vouchers = Voucher::paginate(20);

        $stats = [
            'total' => Voucher::count(),
            'active' => Voucher::where('expiry_date', '>=', now())->orWhereNull('expiry_date')->count(),
            'expired' => Voucher::where('expiry_date', '<', now())->count(),
            'avg_discount' => Voucher::avg('discount_percent') ?? 0
        ];

        return view('admin.vouchers', compact('vouchers', 'stats'));
    }

    public function storeVoucher(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:vouchers,code',
            'discount_percent' => 'required|integer|min:0|max:100',
            'max_discount' => 'nullable|numeric',
            'expiry_date' => 'nullable|date',
        ]);
        Voucher::create($data);
        return back()->with('success', 'Tạo Voucher thành công.');
    }

    public function updateVoucher(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        $data = $request->validate([
            'code' => 'required|string|unique:vouchers,code,'.$id,
            'discount_percent' => 'required|integer|min:0|max:100',
            'max_discount' => 'nullable|numeric',
            'expiry_date' => 'nullable|date',
        ]);
        $voucher->update($data);
        return back()->with('success', 'Cập nhật Voucher thành công.');
    }

    public function destroyVoucher($id)
    {
        Voucher::destroy($id);
        return back()->with('success', 'Đã xoá Voucher.');
    }

    // --- Logistics Routes ---
    public function routes(Request $request)
    {
        // View needs $routes variable instead of $logisticsRoutes
        $routes = LogisticsRoute::with(['shipper.user'])->paginate(20);
        return view('admin.routes', compact('routes'));
    }

    public function storeRoute(Request $request)
    {
        $data = $request->validate([
            'shipper_id' => 'nullable|exists:shippers,id',
            'total_distance' => 'nullable|numeric',
            'estimated_time' => 'nullable|integer',
            'order_ids' => 'nullable|array',
            'order_ids.*' => 'exists:orders,id',
        ]);
        
        DB::beginTransaction();
        try {
            $route = LogisticsRoute::create([
                'shipper_id' => $data['shipper_id'] ?? null,
                'total_distance' => $data['total_distance'] ?? 0,
                'estimated_time' => $data['estimated_time'] ?? 0,
            ]);

            if (!empty($data['order_ids'])) {
                foreach ($data['order_ids'] as $index => $orderId) {
                    \App\Models\RouteOrder::create([
                        'route_id' => $route->id,
                        'order_id' => $orderId,
                        'stop_sequence' => $index + 1,
                    ]);
                    
                    Order::where('id', $orderId)->update([
                        'assignment_status' => 'assigned',
                        'shipper_id' => $data['shipper_id'] ?? null
                    ]);
                }
            }
            
            DB::commit();
            return back()->with('success', 'Tạo tuyến đường thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    public function updateRoute(Request $request, $id)
    {
        $route = LogisticsRoute::findOrFail($id);
        $data = $request->validate([
            'shipper_id' => 'nullable|exists:shippers,id',
            'total_distance' => 'nullable|numeric',
            'estimated_time' => 'nullable|integer',
        ]);
        $route->update($data);
        if (array_key_exists('shipper_id', $data)) {
            $orderIds = \App\Models\RouteOrder::where('route_id', $route->id)->pluck('order_id');
            Order::whereIn('id', $orderIds)->update([
                'shipper_id' => $data['shipper_id'],
                'assignment_status' => empty($data['shipper_id']) ? 'unassigned' : 'assigned'
            ]);
        }
        return back()->with('success', 'Cập nhật tuyến đường thành công.');
    }

    public function assignShipperToRoute(Request $request, $id)
    {
        $route = LogisticsRoute::findOrFail($id);
        $data = $request->validate([
            'shipper_id' => 'required|exists:shippers,id',
        ]);
        $route->update($data);
        
        $orderIds = \App\Models\RouteOrder::where('route_id', $route->id)->pluck('order_id');
        Order::whereIn('id', $orderIds)->update([
            'shipper_id' => $data['shipper_id'],
            'assignment_status' => empty($data['shipper_id']) ? 'unassigned' : 'assigned'
        ]);

        return back()->with('success', 'Đã gán Shipper cho tuyến đường.');
    }

    public function destroyRoute($id)
    {
        LogisticsRoute::destroy($id);
        return back()->with('success', 'Đã xoá tuyến đường.');
    }


    // --- Users Management ---
    public function users(Request $request)
    {
        $users = User::paginate(20);

        $stats = [
            'total' => User::count(),
            'admins' => User::where('role_id', 1)->count(),
            'shippers' => User::where('role_id', 2)->count(),
            'customers' => User::where('role_id', 3)->count(),
            'inactive' => User::where('status', 0)->count()
        ];

        $roles = Role::all();

        return view('admin.users', compact('users', 'stats', 'roles'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'username' => 'required|string',
            'phone' => 'nullable|string',
            'role_id' => 'required|integer',
        ]);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return back()->with('success', 'Cập nhật User thành công.');
    }

    public function destroyUser($id)
    {
        User::destroy($id);
        return back()->with('success', 'Đã xoá User.');
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = !$user->status;
        $user->save();
        return back()->with('success', 'Đã thay đổi trạng thái User.');
    }
}
