@extends('layouts.shipper')

@section('content')

@php
    $activeTab = request('tab', 'dashboard');
    if (request()->has('page')) $activeTab = 'history';
@endphp

@if($activeTab === 'dashboard')
<!-- ================= SECTION 1: DASHBOARD OVERVIEW ================= -->
<div id="section-dashboard" class="space-y-6">
    
    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card: Wallet Balance -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-650 rounded-2xl p-4 text-white shadow-md shadow-blue-500/10">
            <span class="text-[10px] text-blue-100 font-bold uppercase tracking-wider block">Ví tài khoản</span>
            <span class="text-xl font-extrabold mt-1 block">{{ number_format($stats['wallet_balance']) }}đ</span>
            <div class="mt-2.5 flex items-center text-[9px] text-blue-100 font-medium">
                <span>Thu nhập trực tuyến</span>
            </div>
        </div>

        <!-- Card: Assigned Confirmation -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đơn chờ nhận</span>
            <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total_assigned'] }}</span>
            <div class="mt-2.5 flex items-center text-[9px] text-amber-600 font-bold">
                <span class="h-1.5 w-1.5 rounded-full bg-amber-500 mr-1 animate-pulse"></span>
                <span>Yêu cầu mới gán</span>
            </div>
        </div>

        <!-- Card: Delivering -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đang giao</span>
            <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total_processing'] }}</span>
            <div class="mt-2.5 flex items-center text-[9px] text-blue-600 font-bold">
                <span class="h-1.5 w-1.5 rounded-full bg-blue-500 mr-1 animate-pulse"></span>
                <span>Đang đi giao hàng</span>
            </div>
        </div>

        <!-- Card: Total Completed -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đã giao thành công</span>
            <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total_delivered'] }}</span>
            <div class="mt-2.5 flex items-center text-[9px] text-emerald-600 font-bold">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1"></span>
                <span>Hoàn thành xuất sắc</span>
            </div>
        </div>
    </div>

    <!-- Active Deliveries (Processing) Section -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Đơn hàng đang xử lý ({{ $deliveringOrders->count() }})</h3>
            <span class="text-[10px] text-slate-400">Vận chuyển hiện tại</span>
        </div>

                @if($activeRoute)
        <!-- Route Banner -->
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row items-center justify-between mb-4">
            <div class="flex items-center space-x-4">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Bạn đang có 1 Tuyến đường hoạt động</h4>
                    <p class="text-xs text-slate-500 mt-1">Tuyến đường gồm {{ $activeRoute->routeOrders->count() }} điểm giao nhận. Vui lòng thao tác trên Bản đồ.</p>
                </div>
            </div>
            <a href="?tab=route" class="mt-4 md:mt-0 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md transition whitespace-nowrap">
                Mở Bản đồ Lộ trình
            </a>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse($deliveringOrders as $order)
                <div class="bg-white rounded-2xl p-5 border border-slate-200/60 shadow-sm hover:shadow-md transition duration-150 flex flex-col justify-between space-y-4">
                    <!-- Card Top -->
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-xs font-extrabold text-blue-600 font-mono">{{ $order->order_code }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Nguồn: {{ $order->hub->name ?? 'Hub' }}</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-100">
                            Đang đi giao
                        </span>
                    </div>

                    <!-- Pickup -> Delivery Map preview / details -->
                    <div class="space-y-3 relative pl-3.5 text-xs text-slate-700">
                        <div class="absolute left-[3px] top-2 bottom-2 w-0.5 bg-slate-200"></div>

                        <!-- Pickup Point -->
                        <div class="relative flex items-start space-x-2">
                            <span class="absolute left-[-17px] top-1.5 h-2 w-2 rounded-full bg-blue-500 border-2 border-white ring-2 ring-blue-100"></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">Địa chỉ lấy hàng</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ $order->pickup_address }}</span>
                            </div>
                        </div>

                        <!-- Delivery Point -->
                        <div class="relative flex items-start space-x-2">
                            <span class="absolute left-[-17px] top-1.5 h-2 w-2 rounded-full bg-emerald-500 border-2 border-white ring-2 ring-emerald-100"></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">Địa chỉ giao hàng</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ $order->delivery_address }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="bg-slate-50 border border-slate-150 p-3 rounded-xl flex justify-between items-center text-[11px] text-slate-600">
                        <div>
                            <span class="text-slate-400 block">Khách hàng nhận</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $order->customer->user->username ?? 'Khách lẻ' }}</span>
                            <span class="text-slate-400 font-medium block">{{ $order->customer->user->phone ?? '' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-400 block">Số tiền COD / Cước</span>
                            <span class="font-extrabold text-slate-800 text-xs mt-0.5 block">{{ number_format($order->shipping_fee) }}đ</span>
                            <span class="text-slate-400 block">Khối lượng: {{ $order->total_weight }}kg</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <!-- Complete Button -->
                        <form action="{{ route('shipper.orders.delivered', $order->id) }}" method="POST" onsubmit="return confirm('Xác nhận đã giao hàng thành công đơn {{ $order->order_code }} và thu đủ tiền?');" class="w-full">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition shadow-sm hover:shadow-md">
                                <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Giao thành công
                            </button>
                        </form>

                        <!-- Fail trigger Button -->
                        <button onclick="openFailureModal({{ $order->id }}, '{{ $order->order_code }}')" class="w-full inline-flex items-center justify-center py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 rounded-xl text-xs font-bold transition">
                            <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Giao thất bại
                        </button>
                        
                        <!-- Route Map details toggle -->
                        <button data-order='{{ json_encode($order) }}' onclick="openRouteDetails(JSON.parse(this.dataset.order))" class="col-span-2 w-full inline-flex items-center justify-center py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-xl text-xs font-semibold transition border border-slate-200/50">
                            <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" />
                            </svg>
                            Xem bản đồ & lộ trình tối ưu
                        </button>
                    </div>
                </div>
            @empty
                @if($assignedRoutes->isEmpty())
                <div class="col-span-2 bg-white rounded-2xl p-8 border border-slate-200/60 shadow-sm text-center">
                    <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Chưa có yêu cầu điều phối mới</h4>
                    <p class="text-xs text-slate-400 mt-1">Hệ thống chưa gán thêm đơn hàng mới nào cần xác nhận.</p>
                </div>
                @endif
            @endforelse
        </div>
    </div>

    <!-- Assigned Confirms (Waiting for acceptance) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between border-t border-slate-200/60 pt-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Đơn chờ xác nhận từ bạn ({{ $assignedSingleOrders->count() + $assignedRoutes->count() }})</h3>
            <span class="text-[10px] text-amber-600 font-bold animate-pulse">Cần xử lý gấp</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Assigned Routes -->
            @foreach($assignedRoutes as $route)
                <div class="bg-gradient-to-br from-indigo-50 to-white rounded-2xl p-5 border border-indigo-200/60 shadow-sm hover:shadow-md transition duration-150 flex flex-col justify-between space-y-4 col-span-1 lg:col-span-2">
                    <div class="flex items-center justify-between border-b border-indigo-100/50 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7"/></svg>
                            </span>
                            <div>
                                <span class="text-sm font-extrabold text-indigo-700">Tuyến đường nguyên chuyến (Mới)</span>
                                <span class="text-[10px] text-slate-500 block mt-0.5">Gồm {{ $route->routeOrders->count() }} đơn hàng | Quãng đường: {{ $route->total_distance }}km</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 animate-pulse">
                            Chờ nhận tuyến
                        </span>
                    </div>
                    
                    <div class="bg-indigo-50/50 rounded-xl p-3 text-xs text-slate-600">
                        <p class="font-medium text-indigo-800 mb-2">Thứ tự các điểm dừng:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($route->routeOrders->sortBy('stop_sequence')->take(3) as $ro)
                                <span class="bg-white border border-indigo-100 px-2 py-1 rounded-md text-[10px]">{{ $ro->order->order_code }}</span>
                            @endforeach
                            @if($route->routeOrders->count() > 3)
                                <span class="bg-slate-100 border border-slate-200 px-2 py-1 rounded-md text-[10px] text-slate-500">+{{ $route->routeOrders->count() - 3 }} đơn nữa</span>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('shipper.routes.accept', $route->id) }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-sm hover:shadow-md">
                            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            Nhận toàn bộ tuyến đường ({{ $route->routeOrders->count() }} đơn)
                        </button>
                    </form>
                </div>
            @endforeach

            <!-- Assigned Single Orders -->
            @forelse($assignedSingleOrders as $order)
                <div class="bg-gradient-to-b from-amber-50/20 to-white rounded-2xl p-5 border border-amber-200/60 shadow-sm hover:shadow-md transition duration-150 flex flex-col justify-between space-y-4">
                    <!-- Top -->
                    <div class="flex items-center justify-between border-b border-amber-100/50 pb-3">
                        <div>
                            <span class="text-xs font-extrabold text-amber-600 font-mono">{{ $order->order_code }}</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5">Lộ trình được đề xuất</span>
                        </div>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            Chờ nhận đơn
                        </span>
                    </div>

                    <!-- Pickup -> Delivery -->
                    <div class="space-y-3 relative pl-3.5 text-xs text-slate-700">
                        <div class="absolute left-[3px] top-2 bottom-2 w-0.5 bg-slate-200"></div>

                        <!-- Pickup Point -->
                        <div class="relative flex items-start space-x-2">
                            <span class="absolute left-[-17px] top-1.5 h-2 w-2 rounded-full bg-blue-500 border-2 border-white ring-2 ring-blue-100"></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">Địa chỉ lấy hàng</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ $order->pickup_address }}</span>
                            </div>
                        </div>

                        <!-- Delivery Point -->
                        <div class="relative flex items-start space-x-2">
                            <span class="absolute left-[-17px] top-1.5 h-2 w-2 rounded-full bg-emerald-500 border-2 border-white ring-2 ring-emerald-100"></span>
                            <div>
                                <span class="text-[10px] text-slate-400 block font-semibold">Địa chỉ giao hàng</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ $order->delivery_address }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Info Panel -->
                    <div class="bg-amber-50/30 border border-amber-100 p-3 rounded-xl flex justify-between items-center text-[11px] text-slate-600">
                        <div>
                            <span class="text-slate-400 block">Khách hàng nhận</span>
                            <span class="font-bold text-slate-800 mt-0.5 block">{{ $order->customer->user->username ?? 'Khách lẻ' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-slate-400 block">Tiền COD</span>
                            <span class="font-extrabold text-slate-800 text-xs mt-0.5 block">{{ number_format($order->shipping_fee) }}đ</span>
                        </div>
                    </div>

                    <!-- Accept/Decline Actions -->
                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <!-- Accept Button -->
                        <form action="{{ route('shipper.orders.accept', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm hover:shadow-md">
                                <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                Nhận đơn hàng
                            </button>
                        </form>

                        <!-- Decline Button -->
                        <form action="{{ route('shipper.orders.decline', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn từ chối đơn hàng {{ $order->order_code }}?')">
                            @csrf
                            <button type="submit" class="w-full inline-flex items-center justify-center py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 rounded-xl text-xs font-bold transition">
                                <svg class="h-3.5 w-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Từ chối nhận
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                @if($assignedRoutes->isEmpty())
                <div class="col-span-2 bg-white rounded-2xl p-8 border border-slate-200/60 shadow-sm text-center">
                    <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Chưa có yêu cầu điều phối mới</h4>
                    <p class="text-xs text-slate-400 mt-1">Hệ thống chưa gán thêm đơn hàng mới nào cần xác nhận.</p>
                </div>
                @endif
            @endforelse
        </div>
    </div>
</div>
@endif


@if($activeTab === 'route')
<!-- ================= SECTION 1.5: ACTIVE ROUTE MAP ================= -->
<div id="section-route" class="space-y-6">
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-slate-100 pb-4 mb-4">
            <div>
                <h3 class="text-sm font-extrabold uppercase tracking-wider text-slate-800 flex items-center">
                    <span class="h-2 w-2 rounded-full bg-blue-600 mr-2 animate-pulse"></span>
                    Hành trình giao hàng tối ưu (Road Navigation)
                </h3>
                <p class="text-xs text-slate-400 mt-1">Đường đi ngắn nhất nối từ Trạm xuất phát (Hub) qua các điểm Lấy & Giao hàng của bạn.</p>
            </div>
            @if($activeRoute)
                <div class="grid grid-cols-2 divide-x divide-slate-200 text-xs bg-slate-50 border border-slate-200 p-2.5 rounded-xl w-full lg:w-auto lg:flex lg:items-center lg:gap-4 lg:divide-x-0">
                    <div class="text-center lg:text-left lg:pr-2">
                        <span class="text-slate-400 block text-[9px] uppercase font-bold">Tổng quãng đường</span>
                        <span class="font-bold text-slate-800">{{ $activeRoute->total_distance }} km</span>
                    </div>
                    <div class="text-center lg:text-left pl-3 lg:pl-0">
                        <span class="text-slate-400 block text-[9px] uppercase font-bold">Thời gian ước tính</span>
                        <span class="font-bold text-slate-800">{{ $activeRoute->estimated_time }} phút</span>
                    </div>
                </div>
            @endif
        </div>

        @if($activeRoute && $activeRoute->routeOrders->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Map Container (2/3 width on desktop) -->
                <div class="lg:col-span-2 space-y-3">
                    <div id="shipper_trip_map" class="h-64 sm:h-96 lg:h-[480px] w-full rounded-2xl border border-slate-200 shadow-inner relative z-10"></div>
                    <div class="grid grid-cols-3 gap-1 md:flex md:items-center md:justify-between text-[10px] md:text-[11px] text-slate-400 font-medium bg-slate-50 p-2.5 rounded-xl border border-slate-150">
                        <span class="flex items-center justify-center md:justify-start"><span class="h-2 w-2 rounded-full bg-red-500 mr-1.5"></span>Hub</span>
                        <span class="flex items-center justify-center md:justify-start"><span class="h-2 w-2 rounded-full bg-blue-500 mr-1.5"></span>Lấy hàng</span>
                        <span class="flex items-center justify-center md:justify-start"><span class="h-2 w-2 rounded-full bg-emerald-500 mr-1.5"></span>Giao hàng</span>
                    </div>

                    <!-- Navigation instructions panel for active route -->
                    <div class="bg-white border border-slate-200/60 rounded-2xl p-4 shadow-sm space-y-3 mt-3">
                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5 border-b border-slate-100 pb-2">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" />
                            </svg>
                            <span>Chỉ dẫn hành trình di chuyển chi tiết</span>
                        </h4>
                        <div id="trip_instructions_container" class="text-[11px] text-slate-650 space-y-1 max-h-48 overflow-y-auto pr-1">
                            <p class="text-slate-400 italic">Hệ thống sẽ vẽ tuyến đường và hiển thị chỉ dẫn chi tiết ở đây.</p>
                        </div>
                    </div>
                </div>

                <!-- Stops sequence (1/3 width on desktop) -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Thứ tự các điểm dừng:</h4>
                    <div class="space-y-3 max-h-[440px] overflow-y-auto pr-1">
                        <!-- Starting Hub Point -->
                        @php
                            $firstRouteOrder = $activeRoute->routeOrders->sortBy('stop_sequence')->first();
                            $hub = $firstRouteOrder && $firstRouteOrder->order ? $firstRouteOrder->order->hub : null;
                        @endphp
                        @if($hub)
                            <div class="relative pl-7 pb-2 border-l border-slate-200 last:border-0">
                                <span class="absolute left-[-9px] top-1 h-4.5 w-4.5 rounded-full bg-rose-50 border-2 border-rose-500 flex items-center justify-center shadow-md">
                                    <svg class="h-2.5 w-2.5 text-rose-500 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                </span>
                                <div class="bg-rose-50/20 border border-rose-105/50 p-3 rounded-xl">
                                    <span class="text-[9px] font-bold uppercase text-rose-500 block">Xuất phát từ Hub</span>
                                    <h5 class="font-bold text-slate-800 text-xs mt-0.5">{{ $hub->name }}</h5>
                                    <p class="text-[10px] text-slate-500 mt-1">{{ $hub->address }}</p>
                                    @if($hub->latitude && $hub->longitude)
                                        <button type="button" onclick="focusTripMarker({{ $hub->latitude }}, {{ $hub->longitude }}, '{{ addslashes($hub->address) }}')" class="inline-flex items-center mt-2 text-[10px] text-blue-600 hover:text-blue-855 font-bold gap-1 focus:outline-none">
                                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            Chỉ đường trên bản đồ
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Route Orders Stops -->
                        @foreach($activeRoute->routeOrders->sortBy('stop_sequence') as $ro)
                            @php
                                $ord = $ro->order;
                            @endphp
                            @if($ord)
                                <!-- Stop 1: Pickup Point -->
                                <div class="relative pl-7 pb-4 border-l border-slate-200 last:border-0">
                                    <span class="absolute left-[-9px] top-1 h-4.5 w-4.5 rounded-full bg-blue-50 border-2 border-blue-600 flex items-center justify-center text-[9px] font-mono font-bold text-blue-750 shadow-md">
                                        {{ $ro->stop_sequence * 2 - 1 }}
                                    </span>
                                    <div class="bg-white border border-slate-200 p-3 rounded-xl hover:shadow-md transition">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[9px] font-bold uppercase text-blue-600">Điểm lấy hàng</span>
                                            <span class="text-[9px] font-bold text-slate-400 font-mono">{{ $ord->order_code }}</span>
                                        </div>
                                        <h5 class="font-bold text-slate-800 text-xs mt-0.5">{{ $ord->pickup_address }}</h5>
                                        <div class="mt-2 flex items-center justify-between border-t border-slate-100 pt-2">
                                            <button type="button" onclick="focusTripMarker({{ $ord->pickup_lat }}, {{ $ord->pickup_lng }}, '{{ addslashes($ord->pickup_address) }}')" class="text-[10px] text-blue-600 hover:text-blue-800 font-bold inline-flex items-center gap-1 focus:outline-none">
                                                <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                Chỉ đường
                                            </button>
                                            @if($ord->status === 'pending')
                                                <form action="{{ route('shipper.orders.accept', $ord->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="px-2 py-1 bg-blue-600 text-white rounded text-[9px] font-bold hover:bg-blue-700 transition">Xác nhận lấy</button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center text-[9px] text-emerald-600 font-bold">
                                                    <svg class="h-3 w-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                                    Đã lấy hàng
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Stop 2: Delivery Point -->
                                <div class="relative pl-7 pb-4 border-l border-slate-200 last:border-0">
                                    <span class="absolute left-[-9px] top-1 h-4.5 w-4.5 rounded-full bg-emerald-50 border-2 border-emerald-600 flex items-center justify-center text-[9px] font-mono font-bold text-emerald-700 shadow-md">
                                        {{ $ro->stop_sequence * 2 }}
                                    </span>
                                    <div class="bg-white border border-slate-200 p-3 rounded-xl hover:shadow-md transition">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[9px] font-bold uppercase text-emerald-600">Điểm giao hàng</span>
                                            <span class="text-[9px] font-bold text-slate-400 font-mono">{{ $ord->order_code }}</span>
                                        </div>
                                        <h5 class="font-bold text-slate-800 text-xs mt-0.5">{{ $ord->delivery_address }}</h5>
                                        <p class="text-[10px] text-slate-450 font-medium mt-1">Khách: {{ $ord->customer->user->username ?? 'N/A' }} | {{ $ord->customer->user->phone ?? 'N/A' }}</p>
                                        <div class="mt-2 flex items-center justify-between border-t border-slate-100 pt-2">
                                            <button type="button" onclick="focusTripMarker({{ $ord->delivery_lat }}, {{ $ord->delivery_lng }}, '{{ addslashes($ord->delivery_address) }}')" class="text-[10px] text-blue-600 hover:text-blue-800 font-bold inline-flex items-center gap-1 focus:outline-none">
                                                <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                Chỉ đường
                                            </button>
                                            
                                            @if($ord->status === 'processing')
                                                <div class="flex items-center gap-1">
                                                    <form action="{{ route('shipper.orders.delivered', $ord->id) }}" method="POST" onsubmit="return confirm('Xác nhận đã giao hàng thành công đơn {{ $ord->order_code }} và thu đủ tiền?');">
                                                        @csrf
                                                        <button type="submit" class="px-2 py-1 bg-emerald-600 text-white rounded text-[9px] font-bold hover:bg-emerald-700 transition">Giao xong</button>
                                                    </form>
                                                    <button onclick="openFailureModal({{ $ord->id }}, '{{ $ord->order_code }}')" class="px-2 py-1 bg-rose-50 text-rose-600 border border-rose-100 rounded text-[9px] font-bold hover:bg-rose-100 transition">Lỗi</button>
                                                </div>
                                            @elseif($ord->status === 'delivered')
                                                <span class="text-[9px] text-emerald-650 font-bold bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">Giao thành công</span>
                                            @elseif($ord->status === 'failed')
                                                <span class="text-[9px] text-rose-600 font-bold bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">Giao thất bại</span>
                                            @else
                                                <span class="text-[9px] text-slate-400 font-bold">{{ $ord->status }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="h-14 w-14 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                    <svg class="h-7 w-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" /></svg>
                </div>
                <h4 class="font-bold text-slate-800 mt-2 text-sm">Chưa gán hành trình tối ưu hôm nay</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-md mx-auto">Vui lòng chờ Admin tính toán lộ trình tối ưu và gán đơn hàng cho bạn trước khi khởi hành.</p>
            </div>
        @endif
    </div>
</div>
@endif

@if($activeTab === 'history')
<!-- ================= SECTION 2: DELIVERY HISTORY (PAGINATED) ================= -->
<div id="section-history" class="space-y-6">
    <!-- Filters / Summary -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Lịch sử giao nhận của bạn</h3>
            <p class="text-xs text-slate-400 mt-1">Danh sách tất cả các đơn hàng bạn đã giao hoặc báo cáo thất bại.</p>
        </div>
    </div>

    <!-- History List Card -->
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h4 class="font-bold text-slate-800 text-sm">Danh sách chi tiết</h4>
                <p class="text-[11px] text-slate-400 font-medium">Bảng theo dõi các đơn hàng lịch sử</p>
            </div>
            
            <form id="historyFilterForm" class="flex items-center gap-2">
                <input type="text" name="search" id="historySearchInput" placeholder="Tìm tên khách, SĐT, mã đơn..." value="{{ request('search') }}" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 w-48 transition">
                <select name="status" id="historyStatusSelect" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Thành công</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                </select>
                <!-- Keep track of tab -->
                <input type="hidden" name="tab" value="history">
            </form>
        </div>
        <!-- Desktop view -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Mã đơn hàng</th>
                        <th class="px-6 py-4">Khách hàng / Liên hệ</th>
                        <th class="px-6 py-4">Địa chỉ giao nhận</th>
                        <th class="px-6 py-4">Tiền COD</th>
                        <th class="px-6 py-4 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-right">Thời gian</th>
                    </tr>
                </thead>
                <tbody id="historyListDesktop" class="divide-y divide-slate-100 text-xs text-slate-700">
                    @include('shipper.partials.history_list_desktop', ['historyOrders' => $historyOrders])
                </tbody>
            </table>
        </div>

        <!-- Mobile view -->
        <div id="historyListMobile" class="block md:hidden divide-y divide-slate-100">
            @include('shipper.partials.history_list_mobile', ['historyOrders' => $historyOrders])
        </div>

        @if($historyOrders->hasMorePages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 text-center" id="loadMoreHistoryContainer">
                <button id="loadMoreHistoryBtn" data-page="2" class="inline-flex items-center justify-center px-5 py-2 text-xs font-bold bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl transition">
                    Tải thêm lịch sử
                </button>
            </div>
        @endif
    </div>

    <!-- AJAX Lazy Loading Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loadMoreBtn = document.getElementById('loadMoreHistoryBtn');
            const listDesktop = document.getElementById('historyListDesktop');
            const listMobile = document.getElementById('historyListMobile');
            const searchInput = document.getElementById('historySearchInput');
            const statusSelect = document.getElementById('historyStatusSelect');
            const loadMoreContainer = document.getElementById('loadMoreHistoryContainer');
            let isLoading = false;

            // Auto submit filter on change
            let debounceTimer;
            if(searchInput) {
                searchInput.addEventListener('input', () => {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => submitFilter(), 500);
                });
            }
            if(statusSelect) {
                statusSelect.addEventListener('change', () => submitFilter());
            }

            function submitFilter() {
                const url = new URL(window.location.href);
                url.searchParams.set('search', searchInput.value);
                url.searchParams.set('status', statusSelect.value);
                url.searchParams.set('page', 1);
                url.searchParams.set('tab', 'history');
                window.location.href = url.toString();
            }

            if (loadMoreBtn) {
                loadMoreBtn.addEventListener('click', function() {
                    if (isLoading) return;
                    isLoading = true;
                    
                    const originalText = loadMoreBtn.innerHTML;
                    loadMoreBtn.innerHTML = 'Đang tải...';
                    loadMoreBtn.disabled = true;

                    const page = this.getAttribute('data-page');
                    const url = new URL(window.location.href);
                    url.searchParams.set('page', page);
                    url.searchParams.set('search', searchInput.value || '');
                    url.searchParams.set('status', statusSelect.value || 'all');
                    url.searchParams.set('tab', 'history');

                    fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.desktop_html && listDesktop) {
                            listDesktop.insertAdjacentHTML('beforeend', data.desktop_html);
                        }
                        if(data.mobile_html && listMobile) {
                            listMobile.insertAdjacentHTML('beforeend', data.mobile_html);
                        }
                        if(data.hasMorePages) {
                            loadMoreBtn.setAttribute('data-page', parseInt(page) + 1);
                        } else {
                            if(loadMoreContainer) loadMoreContainer.style.display = 'none';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching history:', error);
                        alert('Đã xảy ra lỗi khi tải thêm dữ liệu.');
                    })
                    .finally(() => {
                        isLoading = false;
                        loadMoreBtn.innerHTML = originalText;
                        loadMoreBtn.disabled = false;
                    });
                });
            }
        });
    </script>
    </div>
</div>
@endif


<!-- ================= MODALS ================= -->

<!-- 1. Report Delivery Failure Modal -->
<div id="failureModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Báo cáo đơn hàng thất bại</h3>
            <button onclick="closeFailureModal()" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="failureForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Mã đơn hàng</label>
                <input type="text" id="fail_order_code" disabled class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs font-mono font-bold text-slate-500">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Lý do thất bại *</label>
                <select name="reason" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-xs focus:bg-white focus:outline-none">
                    <option value="Khách hàng không liên lạc được (Gọi 3 lần)">Khách hàng không liên lạc được (Gọi 3 lần)</option>
                    <option value="Khách từ chối nhận hàng (Không đúng mô tả)">Khách từ chối nhận hàng (Không đúng mô tả)</option>
                    <option value="Khách hẹn giao lại ngày khác">Khách hẹn giao lại ngày khác</option>
                    <option value="Sai số điện thoại / Địa chỉ người nhận">Sai số điện thoại / Địa chỉ người nhận</option>
                    <option value="Sự cố phương tiện vận chuyển">Sự cố phương tiện vận chuyển</option>
                    <option value="Lý do khác...">Lý do khác...</option>
                </select>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeFailureModal()" class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl text-white bg-rose-600 hover:bg-rose-700">Xác nhận báo lỗi</button>
            </div>
        </form>
    </div>
</div>

<!-- 1.5. Confirm Delivery Success Modal -->
<div id="successConfirmModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between bg-emerald-50/50">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Xác nhận giao hàng thành công</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Vui lòng kiểm tra kỹ thông tin đơn hàng trước khi hoàn thành</p>
            </div>
            <button onclick="closeSuccessConfirmModal()" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="successConfirmForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="space-y-3">
                <div class="bg-slate-50 border border-slate-150 p-4 rounded-xl space-y-2">
                    <div class="flex justify-between text-xs border-b border-slate-100 pb-2">
                        <span class="text-slate-400 font-medium">Mã đơn hàng:</span>
                        <span class="font-bold text-blue-600 font-mono" id="success_order_code">N/A</span>
                    </div>
                    <div class="flex justify-between text-xs border-b border-slate-100 pb-2">
                        <span class="text-slate-400 font-medium">Khách hàng nhận:</span>
                        <span class="font-bold text-slate-800" id="success_customer_name">N/A</span>
                    </div>
                    <div class="flex justify-between text-xs border-b border-slate-100 pb-2">
                        <span class="text-slate-400 font-medium">Tiền cước shipper nhận:</span>
                        <span class="font-extrabold text-emerald-600 text-sm" id="success_shipping_fee">0đ</span>
                    </div>
                    <div class="text-xs">
                        <span class="text-slate-400 font-medium block mb-1">Địa chỉ giao hàng:</span>
                        <span class="font-medium text-slate-700" id="success_delivery_address">N/A</span>
                    </div>
                </div>

                <p class="text-[10px] text-amber-655 font-bold bg-amber-50 p-2.5 rounded-lg border border-amber-100/50">
                    * Lưu ý: Khi xác nhận giao xong, trạng thái đơn sẽ chuyển sang "Đã giao hàng" và tiền cước sẽ được cộng trực tiếp vào ví của bạn.
                </p>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeSuccessConfirmModal()" class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm shadow-emerald-500/10">Xác nhận hoàn thành</button>
            </div>
        </form>
    </div>
</div>
<!-- 2. Route details / MAP Modal (Single Order) -->
<div id="routeDetailsModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl border border-slate-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between bg-slate-50">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Lộ trình tối ưu hành trình</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Thông tin toạ độ và chuỗi thứ tự điểm dừng tối ưu của đơn hàng</p>
            </div>
            <button onclick="closeRouteDetails()" class="text-slate-400 hover:text-slate-655">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-6">
            <!-- Interactive Leaflet Map for Shipper -->
            <div id="shipper_detail_map" class="h-64 w-full bg-slate-100 rounded-xl border border-slate-200 relative z-10 shadow-inner"></div>

            <!-- Single Order Directions -->
            <div class="space-y-2">
                <h4 class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" />
                    </svg>
                    <span>Hướng dẫn đường đi từng chặng</span>
                </h4>
                <div id="detail_instructions_container" class="bg-slate-50 border border-slate-200 rounded-xl p-3 max-h-40 overflow-y-auto space-y-1 text-[11px] text-slate-650">
                    <p class="text-slate-400 italic">Đang tính toán tuyến đường...</p>
                </div>
            </div>

            <!-- Route detail lists -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="space-y-2.5">
                    <h4 class="font-bold text-slate-800 flex items-center">
                        <span class="h-2 w-2 rounded-full bg-blue-500 mr-1.5"></span>
                        Tọa độ điểm đi (Pickup)
                    </h4>
                    <div class="bg-slate-50 border border-slate-200 p-3 rounded-xl space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Vĩ độ (Lat):</span>
                            <span class="font-mono font-semibold text-slate-800" id="route_pickup_lat">N/A</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Kinh độ (Lng):</span>
                            <span class="font-mono font-semibold text-slate-800" id="route_pickup_lng">N/A</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <h4 class="font-bold text-slate-800 flex items-center">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 mr-1.5"></span>
                        Tọa độ điểm đến (Delivery)
                    </h4>
                    <div class="bg-slate-50 border border-slate-200 p-3 rounded-xl space-y-1">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Vĩ độ (Lat):</span>
                            <span class="font-mono font-semibold text-slate-800" id="route_delivery_lat">N/A</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Kinh độ (Lng):</span>
                            <span class="font-mono font-semibold text-slate-800" id="route_delivery_lng">N/A</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-slate-50 px-6 py-4 border-t border-slate-150 flex justify-end">
            <button onclick="closeRouteDetails()" class="px-5 py-2 text-xs font-bold rounded-xl text-slate-650 bg-slate-200/80 hover:bg-slate-200">Đóng lộ trình</button>
        </div>
    </div>
</div>

<script>
    let shipperMap = null;
    let shipperMapLayers = [];
    let shipperTripMarkers = [];

    // 1. Failure modal operations
    function openFailureModal(orderId, orderCode) {
        document.getElementById('fail_order_code').value = orderCode;
        const form = document.getElementById('failureForm');
        form.action = `/shipper/orders/${orderId}/fail`;
        document.getElementById('failureModal').classList.remove('hidden');
    }

    // Helper translation function for OSRM steps
    function translateStepManeuver(step, index) {
        if (!step || !step.maneuver) return "";
        let type = step.maneuver.type;
        let modifier = step.maneuver.modifier;
        let streetName = step.name ? `<b>${step.name}</b>` : "";
        let distanceVal = step.distance;
        let distanceStr = "";
        if (distanceVal > 0) {
            if (distanceVal >= 1000) {
                distanceStr = ` (${(distanceVal / 1000).toFixed(1)} km)`;
            } else {
                distanceStr = ` (${Math.round(distanceVal)} m)`;
            }
        }

        let instructionText = "";
        let iconSvg = "";

        // Direction Arrow SVGs
        const svgGoStraight = `<svg class="h-4 w-4 text-slate-500 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>`;
        const svgTurnLeft = `<svg class="h-4 w-4 text-blue-500 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>`;
        const svgTurnRight = `<svg class="h-4 w-4 text-indigo-500 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>`;
        const svgArrive = `<svg class="h-4 w-4 text-emerald-500 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
        const svgDepart = `<svg class="h-4 w-4 text-rose-500 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;

        iconSvg = svgGoStraight;

        switch (type) {
            case 'depart':
                instructionText = `Bắt đầu di chuyển ${streetName ? 'từ ' + streetName : ''}`;
                iconSvg = svgDepart;
                break;
            case 'arrive':
                instructionText = `Đã đến điểm dừng/bàn giao`;
                iconSvg = svgArrive;
                break;
            case 'turn':
                if (modifier === 'left' || modifier === 'sharp left') {
                    instructionText = `Rẽ trái vào đường ${streetName || 'phía trước'}`;
                    iconSvg = svgTurnLeft;
                } else if (modifier === 'right' || modifier === 'sharp right') {
                    instructionText = `Rẽ phải vào đường ${streetName || 'phía trước'}`;
                    iconSvg = svgTurnRight;
                } else if (modifier === 'slight left') {
                    instructionText = `Chếch nhẹ sang trái vào đường ${streetName || 'phía trước'}`;
                    iconSvg = svgTurnLeft;
                } else if (modifier === 'slight right') {
                    instructionText = `Chếch nhẹ sang phải vào đường ${streetName || 'phía trước'}`;
                    iconSvg = svgTurnRight;
                } else {
                    instructionText = `Rẽ hướng vào đường ${streetName || 'phía trước'}`;
                }
                break;
            case 'continue':
                instructionText = `Tiếp tục đi thẳng trên đường ${streetName || 'hiện tại'}`;
                iconSvg = svgGoStraight;
                break;
            case 'new name':
                instructionText = `Đi tiếp vào đường ${streetName || 'mới'}`;
                iconSvg = svgGoStraight;
                break;
            case 'roundabout':
                instructionText = `Đi vào vòng xuyến, đi theo lối ra đường ${streetName || 'phía trước'}`;
                iconSvg = svgGoStraight;
                break;
            default:
                instructionText = `Đi tiếp trên đường ${streetName || 'hiện tại'}`;
                break;
        }

        return `
            <div class="flex items-start gap-2 py-1.5 border-b border-slate-100 last:border-0 text-slate-700">
                <span class="flex-shrink-0 mt-0.5">${iconSvg}</span>
                <div class="flex-1">
                    <span class="text-slate-800">${index}. ${instructionText}</span>
                    <span class="text-slate-400 font-semibold font-mono block text-[9px]">${distanceStr}</span>
                </div>
            </div>
        `;
    }

    function closeFailureModal() {
        document.getElementById('failureModal').classList.add('hidden');
    }

    // 2. Route details modal operations (popup for single order)
    function openRouteDetails(order) {
        document.getElementById('route_pickup_lat').innerText = parseFloat(order.pickup_lat).toFixed(6);
        document.getElementById('route_pickup_lng').innerText = parseFloat(order.pickup_lng).toFixed(6);
        document.getElementById('route_delivery_lat').innerText = parseFloat(order.delivery_lat).toFixed(6);
        document.getElementById('route_delivery_lng').innerText = parseFloat(order.delivery_lng).toFixed(6);
        
        document.getElementById('routeDetailsModal').classList.remove('hidden');

        setTimeout(() => {
            if (!shipperMap) {
                shipperMap = L.map('shipper_detail_map').setView([21.028511, 105.804817], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(shipperMap);
            }

            // Clear previous markers & lines
            shipperMapLayers.forEach(layer => shipperMap.removeLayer(layer));
            shipperMapLayers = [];

            let points = [];
            let bounds = [];

            // Add Hub
            if (order.hub && order.hub.latitude && order.hub.longitude) {
                let hubLat = parseFloat(order.hub.latitude);
                let hubLng = parseFloat(order.hub.longitude);
                let hubMarker = L.marker([hubLat, hubLng], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="background:#ef4444;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                                   <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                   <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #ef4444;"></div>
                               </div>`,
                        iconSize: [32, 40],
                        iconAnchor: [16, 40]
                    })
                }).addTo(shipperMap).bindPopup(`<b>Điểm xuất phát (Hub): ${order.hub.name}</b><br><span class="text-xs text-slate-500">${order.hub.address}</span>`);
                shipperMapLayers.push(hubMarker);
                points.push([hubLat, hubLng]);
                bounds.push([hubLat, hubLng]);
            }

            // Add Pickup
            if (order.pickup_lat && order.pickup_lng) {
                let plat = parseFloat(order.pickup_lat);
                let plng = parseFloat(order.pickup_lng);
                let pMarker = L.marker([plat, plng], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="background:#3b82f6;border:2px dashed rgba(255,255,255,0.9);border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                                   <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                   <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #3b82f6;"></div>
                               </div>`,
                        iconSize: [28, 36],
                        iconAnchor: [14, 36]
                    })
                }).addTo(shipperMap).bindPopup(`<b>Điểm lấy hàng (Pickup)</b><br><span class="text-xs text-slate-500">${order.pickup_address}</span>`);
                shipperMapLayers.push(pMarker);
                points.push([plat, plng]);
                bounds.push([plat, plng]);
            }

            // Add Delivery
            if (order.delivery_lat && order.delivery_lng) {
                let dlat = parseFloat(order.delivery_lat);
                let dlng = parseFloat(order.delivery_lng);
                let dMarker = L.marker([dlat, dlng], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="background:#10b981;border:2px solid white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                                   <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                   <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #10b981;"></div>
                               </div>`,
                        iconSize: [28, 36],
                        iconAnchor: [14, 36]
                    })
                }).addTo(shipperMap).bindPopup(`<b>Điểm giao hàng (Delivery)</b><br><span class="text-xs text-slate-500">Người nhận: ${order.customer ? order.customer.user.username : 'N/A'}<br>${order.delivery_address}</span>`);
                shipperMapLayers.push(dMarker);
                points.push([dlat, dlng]);
                bounds.push([dlat, dlng]);
            }

            // Connect using OSRM Road routing API & draw actual road path
            if (points.length > 1) {
                let coordsStr = points.map(pt => `${pt[1]},${pt[0]}`).join(';');
                let osrmUrl = `https://router.project-osrm.org/route/v1/driving/${coordsStr}?overview=full&geometries=geojson&steps=true`;
                
                document.getElementById('detail_instructions_container').innerHTML = '<p class="text-slate-400 italic text-[11px]">Đang tải hướng dẫn đường đi...</p>';

                fetch(osrmUrl)
                    .then(res => res.json())
                    .then(data => {
                        if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                            let routeGeometry = data.routes[0].geometry;
                            let routeLine = L.geoJSON(routeGeometry, {
                                style: {
                                    color: '#2563eb',
                                    weight: 5,
                                    opacity: 0.85
                                }
                            }).addTo(shipperMap);
                            shipperMapLayers.push(routeLine);

                            // Render steps
                            let instructionsHtml = '';
                            let stepIndex = 1;
                            data.routes[0].legs.forEach(leg => {
                                leg.steps.forEach(step => {
                                    instructionsHtml += translateStepManeuver(step, stepIndex++);
                                });
                            });
                            document.getElementById('detail_instructions_container').innerHTML = instructionsHtml || '<p class="text-slate-400">Không có thông tin chi tiết.</p>';
                        } else {
                            // Fallback
                            let straightLine = L.polyline(points, {
                                color: '#3b82f6',
                                dashArray: '8, 8',
                                weight: 3
                            }).addTo(shipperMap);
                            shipperMapLayers.push(straightLine);
                            document.getElementById('detail_instructions_container').innerHTML = '<p class="text-slate-450">Tuyến đường thẳng (không lấy được bản đồ từ OSRM).</p>';
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        let straightLine = L.polyline(points, {
                            color: '#3b82f6',
                            dashArray: '8, 8',
                            weight: 3
                        }).addTo(shipperMap);
                        shipperMapLayers.push(straightLine);
                        document.getElementById('detail_instructions_container').innerHTML = '<p class="text-blue-500 font-semibold">Hiển thị tuyến đường thẳng đứt nét (Lỗi kết nối OSRM).</p>';
                    });
            }

            if (bounds.length > 0) {
                shipperMap.fitBounds(bounds, { padding: [35, 35] });
            }
            shipperMap.invalidateSize();
        }, 250);
    }

    function closeRouteDetails() {
        document.getElementById('routeDetailsModal').classList.add('hidden');
    }

    // 3. Active Route Trip Map operations (tab route)
    let shipperTripMap = null;
    let shipperTripLayers = [];

    window.addEventListener('route-tab-active', () => {
        setTimeout(() => {
            initializeShipperTripMap();
        }, 150);
    });

    function initializeShipperTripMap() {
        const mapDiv = document.getElementById('shipper_trip_map');
        if (!mapDiv) return;

        if (!shipperTripMap) {
            shipperTripMap = L.map('shipper_trip_map').setView([21.028511, 105.804817], 13);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(shipperTripMap);
        }

        // Clear layers
        shipperTripLayers.forEach(layer => shipperTripMap.removeLayer(layer));
        shipperTripLayers = [];
        shipperTripMarkers = [];

        // Prepare points in chronological sequence
        let points = [];
        let bounds = [];

        // Read points from Blade structure (passed as JS object)
        let routeData = @json($activeRoute);
        if (!routeData || !routeData.route_orders || routeData.route_orders.length === 0) return;

        // Sort by stop_sequence
        let routeOrders = routeData.route_orders.sort((a, b) => a.stop_sequence - b.stop_sequence);

        // 1. Add Starting Hub from first order
        let firstOrder = routeOrders[0].order;
        if (firstOrder && firstOrder.hub && firstOrder.hub.latitude && firstOrder.hub.longitude) {
            let hLat = parseFloat(firstOrder.hub.latitude);
            let hLng = parseFloat(firstOrder.hub.longitude);
            let hubMarker = L.marker([hLat, hLng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="background:#ef4444;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                               <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                               <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #ef4444;"></div>
                           </div>`,
                    iconSize: [32, 40],
                    iconAnchor: [16, 40]
                })
            }).addTo(shipperTripMap).bindPopup(`<b>Hub xuất phát: ${firstOrder.hub.name}</b><br>${firstOrder.hub.address}`);
            shipperTripLayers.push(hubMarker);
            shipperTripMarkers.push(hubMarker);
            points.push([hLat, hLng]);
            bounds.push([hLat, hLng]);
        }

        // 2. Loop and add pick up and delivery markers in sequence
        routeOrders.forEach((ro) => {
            let order = ro.order;
            if (!order) return;

            let stopSeq = ro.stop_sequence;

            // Pickup Marker
            if (order.pickup_lat && order.pickup_lng) {
                let pLat = parseFloat(order.pickup_lat);
                let pLng = parseFloat(order.pickup_lng);
                let pickupMarker = L.marker([pLat, pLng], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="background:#3b82f6;border:2px dashed rgba(255,255,255,0.9);border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                                   <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                   <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #3b82f6;"></div>
                               </div>`,
                        iconSize: [28, 36],
                        iconAnchor: [14, 36]
                    })
                }).addTo(shipperTripMap).bindPopup(`<b>Điểm Lấy Hàng ${stopSeq * 2 - 1}</b><br>Đơn hàng: ${order.order_code}<br>${order.pickup_address}`);
                shipperTripLayers.push(pickupMarker);
                shipperTripMarkers.push(pickupMarker);
                points.push([pLat, pLng]);
                bounds.push([pLat, pLng]);
            }

            // Delivery Marker
            if (order.delivery_lat && order.delivery_lng) {
                let dLat = parseFloat(order.delivery_lat);
                let dLng = parseFloat(order.delivery_lng);
                let deliveryMarker = L.marker([dLat, dLng], {
                    icon: L.divIcon({
                        className: '',
                        html: `<div style="background:#10b981;border:2px solid white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                                   <span style="color:white;font-weight:900;font-size:11px;">${stopSeq}</span>
                                   <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #10b981;"></div>
                               </div>`,
                        iconSize: [28, 36],
                        iconAnchor: [14, 36]
                    })
                }).addTo(shipperTripMap).bindPopup(`<b>Điểm Giao Hàng ${stopSeq * 2}</b><br>Đơn hàng: ${order.order_code}<br>${order.delivery_address}`);
                shipperTripLayers.push(deliveryMarker);
                shipperTripMarkers.push(deliveryMarker);
                points.push([dLat, dLng]);
                bounds.push([dLat, dLng]);
            }
        });

        // 3. Draw shortest road path line connecting points in sequence using Project OSRM Routing API
        if (points.length > 1) {
            let coordsStr = points.map(pt => `${pt[1]},${pt[0]}`).join(';');
            let osrmUrl = `https://router.project-osrm.org/route/v1/driving/${coordsStr}?overview=full&geometries=geojson&steps=true`;
            
            document.getElementById('trip_instructions_container').innerHTML = '<p class="text-slate-400 italic text-[11px]">Đang tải hướng dẫn đường đi toàn tuyến...</p>';

            fetch(osrmUrl)
                .then(res => res.json())
                .then(data => {
                    if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                        let routeGeometry = data.routes[0].geometry;
                        let routeLine = L.geoJSON(routeGeometry, {
                            style: {
                                color: '#2563eb',
                                weight: 5,
                                opacity: 0.85
                            }
                        }).addTo(shipperTripMap);
                        shipperTripLayers.push(routeLine);

                        // Render instructions steps
                        let instructionsHtml = '';
                        let stepIndex = 1;
                        data.routes[0].legs.forEach(leg => {
                            leg.steps.forEach(step => {
                                instructionsHtml += translateStepManeuver(step, stepIndex++);
                            });
                        });
                        document.getElementById('trip_instructions_container').innerHTML = instructionsHtml || '<p class="text-slate-400">Không có hướng dẫn.</p>';
                    } else {
                        // Fallback straight line
                        let straightLine = L.polyline(points, {
                            color: '#3b82f6',
                            dashArray: '8, 8',
                            weight: 3
                        }).addTo(shipperTripMap);
                        shipperTripLayers.push(straightLine);
                        document.getElementById('trip_instructions_container').innerHTML = '<p class="text-slate-450">Tuyến đường thẳng (không có thông tin chỉ đường từ OSRM).</p>';
                    }
                })
                .catch(err => {
                    console.error('Error fetching OSRM road route: ', err);
                    // Fallback straight line
                    let straightLine = L.polyline(points, {
                        color: '#3b82f6',
                        dashArray: '8, 8',
                        weight: 3
                    }).addTo(shipperTripMap);
                    shipperTripLayers.push(straightLine);
                    document.getElementById('trip_instructions_container').innerHTML = '<p class="text-blue-500 font-semibold">Hiển thị tuyến đường thẳng đứt nét (Lỗi kết nối OSRM).</p>';
                });
        }

        if (bounds.length > 0) {
            shipperTripMap.fitBounds(bounds, { padding: [40, 40] });
        }
        shipperTripMap.invalidateSize();
    }

    // focusTripMarker function to highlight a specific marker and switch to map tab
    function focusTripMarker(lat, lng, address) {
        setTimeout(() => {
            if (shipperTripMap) {
                shipperTripMap.setView([lat, lng], 16);
                
                // Find matching preloaded marker
                let found = shipperTripMarkers.find(m => {
                    let ll = m.getLatLng();
                    return Math.abs(ll.lat - lat) < 0.0001 && Math.abs(ll.lng - lng) < 0.0001;
                });
                
                if (found) {
                    found.openPopup();
                } else {
                    L.popup()
                        .setLatLng([lat, lng])
                        .setContent(`<b>Vị trí:</b> ${address}`)
                        .openOn(shipperTripMap);
                }
            }
        }, 300);
    }

    // Initialize active map if container exists (meaning Route tab is active)
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('shipper_trip_map')) {
            setTimeout(() => {
                initializeShipperTripMap();
            }, 150);
        }
    });
</script>
@endsection