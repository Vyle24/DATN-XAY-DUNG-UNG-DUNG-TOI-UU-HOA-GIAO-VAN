@extends('layouts.admin')
@section('header_title', 'Hệ Thống Điều Phối Thông Minh')
@section('content')

{{-- Quick Actions Bar --}}
<div class="flex items-center justify-between mb-4 flex-wrap gap-2">
    <div>
        <h2 class="text-lg font-extrabold text-slate-800">Hệ Thống Điều Phối Thông Minh</h2>
        <p class="text-xs text-slate-400 mt-0.5">Gom đơn → Tối ưu tuyến → Phân công Shipper</p>
    </div>
    <div class="flex items-center gap-2">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-4 w-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.242-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <select id="top-region-select" onchange="changeRegionFromTop()" class="pl-9 border border-indigo-200 rounded-xl pr-3 py-2 text-xs font-extrabold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition shadow-sm outline-none cursor-pointer appearance-none">
                <option value="10.7769,106.7009">TP. Hồ Chí Minh</option>
                <option value="21.028511,105.804817">Thủ đô Hà Nội</option>
                <option value="16.0544,108.2022">TP. Đà Nẵng</option>
                <option value="10.0451,105.7468">TP. Cần Thơ</option>
            </select>
        </div>
        <a href="{{ route('admin.routes') }}" class="inline-flex items-center px-3 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200 transition shadow-sm">
            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
            Xem Tuyến Đường
        </a>
    </div>
</div>

{{-- ===== STEP GUIDE STEPPER ===== --}}
<div id="dispatch-stepper" class="mb-5 bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 cursor-pointer select-none" onclick="toggleStepper()">
        <div class="flex items-center gap-2">
            <span class="flex h-2 w-2 relative">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-500"></span>
            </span>
            <span class="text-xs font-bold text-slate-700">Quy Trình Điều Phối — 3 Bước</span>
            <span id="stepper-step-badge" class="text-[10px] font-bold px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100">Đang xác định bước...</span>
        </div>
        <button class="text-slate-400 hover:text-slate-600 text-xs px-2 py-0.5 rounded hover:bg-slate-100">
            <svg id="stepper-chevron" class="h-4 w-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
        </button>
    </div>
    <div id="stepper-body" class="grid grid-cols-1 md:grid-cols-3 divide-y md:divide-y-0 md:divide-x divide-slate-100">
        {{-- Step 1 --}}
        <div id="step-card-1" class="p-4 flex items-start gap-3 transition-all">
            <div id="step-icon-1" class="h-9 w-9 rounded-xl flex items-center justify-center text-sm font-black flex-shrink-0 bg-slate-100 text-slate-400 transition-all">1</div>
            <div class="flex-1">
                <p id="step-title-1" class="text-xs font-bold text-slate-700">Gom Đơn Hàng</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Nhóm các đơn gần nhau vào một tuyến. Chỉnh bán kính → nhấn <b>Thực Hiện Gom Batch</b>.</p>
                <div id="step-status-1" class="mt-1.5 inline-flex items-center gap-1 text-[10px] font-bold rounded-full px-2 py-0.5 bg-slate-100 text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Chờ xử lý
                </div>
            </div>
        </div>
        {{-- Step 2 --}}
        <div id="step-card-2" class="p-4 flex items-start gap-3 transition-all">
            <div id="step-icon-2" class="h-9 w-9 rounded-xl flex items-center justify-center text-sm font-black flex-shrink-0 bg-slate-100 text-slate-400 transition-all">2</div>
            <div class="flex-1">
                <p id="step-title-2" class="text-xs font-bold text-slate-700">Tối Ưu Thứ Tự Giao</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Sắp xếp lộ trình ngắn nhất cho từng tuyến. Nhấn <b>Tối Ưu</b> ở cột giữa.</p>
                <div id="step-status-2" class="mt-1.5 inline-flex items-center gap-1 text-[10px] font-bold rounded-full px-2 py-0.5 bg-slate-100 text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Chờ xử lý
                </div>
            </div>
        </div>
        {{-- Step 3 --}}
        <div id="step-card-3" class="p-4 flex items-start gap-3 transition-all">
            <div id="step-icon-3" class="h-9 w-9 rounded-xl flex items-center justify-center text-sm font-black flex-shrink-0 bg-slate-100 text-slate-400 transition-all">3</div>
            <div class="flex-1">
                <p id="step-title-3" class="text-xs font-bold text-slate-700">Tự Động Phân Công Shipper</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Chọn tuyến chưa gán → nhấn <b>Tự Động Phân Công</b> để gán shipper phù hợp nhất.</p>
                <div id="step-status-3" class="mt-1.5 inline-flex items-center gap-1 text-[10px] font-bold rounded-full px-2 py-0.5 bg-slate-100 text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span> Chờ xử lý
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stats Bar --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-[10px] font-bold uppercase opacity-80">Đơn chờ phân công</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['unassigned'] }}</p>
        <p class="text-[10px] opacity-70 mt-1">Chưa được gán Shipper</p>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-[10px] font-bold uppercase opacity-80">Shipper Online</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['active_shippers'] }}</p>
        <p class="text-[10px] opacity-70 mt-1">Sẵn sàng nhận đơn</p>
    </div>
    <div class="bg-gradient-to-br from-violet-500 to-violet-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-[10px] font-bold uppercase opacity-80">Tuyến đang chạy</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['active_routes'] }}</p>
        <p class="text-[10px] opacity-70 mt-1">Routes hiện có</p>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-4 text-white shadow-lg">
        <p class="text-[10px] font-bold uppercase opacity-80">Đang giao</p>
        <p class="text-3xl font-extrabold mt-1">{{ $stats['total_processing'] }}</p>
        <p class="text-[10px] opacity-70 mt-1">Đơn đang vận chuyển</p>
    </div>
</div>

{{-- 3 Panels --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- PANEL 1: GOM ĐƠN HÀNG --}}
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-blue-100/50 border-b border-blue-100 flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-blue-600 flex items-center justify-center text-white flex-shrink-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-blue-900">1. Gom Đơn Hàng</h3>
                <p class="text-[10px] text-blue-600 font-medium">Batch Delivery — Greedy Clustering</p>
            </div>
        </div>

        <div class="p-5 space-y-4">
            {{-- Preview Map --}}
            <div id="previewMap" class="h-48 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden relative z-10"></div>
            <div class="flex gap-2 text-[10px]">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>Đơn chưa gán</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-full bg-orange-400 inline-block"></span>Cùng batch</span>
            </div>

            <form action="{{ route('admin.dispatch.batch') }}" method="POST" class="space-y-3" id="batchForm">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Bán kính gom (km)</label>
                        <input type="number" name="radius_km" id="radiusKm" value="5" step="0.5" min="0.5" max="50"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="updatePreview()">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Tối đa đơn/batch</label>
                        <input type="number" name="max_orders" id="maxOrders" value="5" min="2" max="20"
                            class="w-full border border-slate-200 rounded-lg px-3 py-2 text-xs bg-slate-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            onchange="updatePreview()">
                    </div>
                </div>
                <div id="previewResult" class="bg-blue-50 border border-blue-100 rounded-lg px-3 py-2 text-xs text-blue-700 hidden"></div>
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-1.5">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    Thực Hiện Gom Batch
                </button>
            </form>

            {{-- Unassigned orders list --}}
            <div class="border-t border-slate-100 pt-3">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Đơn chưa gán ({{ $unassignedOrders->count() }})</p>
                <div class="space-y-1.5 max-h-40 overflow-y-auto pr-1">
                    @forelse($unassignedOrders as $order)
                    <div class="flex items-center justify-between bg-slate-50 rounded-lg px-2.5 py-1.5 text-[11px]">
                        <span class="font-bold text-blue-600">{{ $order->order_code }}</span>
                        <span class="text-slate-400 truncate mx-2 flex-1">{{ Str::limit($order->delivery_address, 25) }}</span>
                        <span class="text-[9px] text-slate-300 font-mono">{{ number_format($order->delivery_lat,4) }},{{ number_format($order->delivery_lng,4) }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-4 flex items-center justify-center gap-1.5">
                        <svg class="h-4 w-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Không có đơn nào chưa gán
                    </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- PANEL 2: TỐI ƯU TUYẾN + AUTO ASSIGN --}}
    <div class="space-y-5">
        {{-- Optimize Routes --}}
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-violet-50 to-violet-100/50 border-b border-violet-100 flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-violet-600 flex items-center justify-center text-white flex-shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-violet-900">2. Tối Ưu Thứ Tự Giao</h3>
                    <p class="text-[10px] text-violet-600 font-medium">Nearest Neighbor Algorithm</p>
                </div>
            </div>
            <div class="p-5">
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @foreach($routes as $route)
                    <div class="border border-slate-200 rounded-xl px-3 py-2.5 text-xs" id="route-card-{{ $route->id }}">
                        <div class="flex items-center justify-between mb-1.5">
                            <div>
                                <span class="font-bold text-slate-800">Tuyến #{{ $route->id }}</span>
                                <span class="text-slate-400 ml-2" id="route-stats-{{ $route->id }}">{{ $route->routeOrders->count() }} điểm · <span class="route-dist-val">{{ $route->total_distance }}</span> km</span>
                            </div>
                            <form action="{{ route('admin.dispatch.optimize', $route->id) }}" method="POST" onsubmit="handleOptimize(event, {{ $route->id }}, this)">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 bg-violet-600 hover:bg-violet-700 text-white text-[10px] font-bold rounded-lg transition" {{ $route->routeOrders->count() < 2 ? 'disabled' : '' }}>
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    Tối ưu
                                </button>
                            </form>
                        </div>
                        <div class="text-[10px] text-slate-400">
                            Shipper: <span class="font-semibold text-slate-600">{{ $route->shipper->user->username ?? 'Chưa gán' }}</span>
                            · Ước tính: <span class="route-time-val">{{ $route->estimated_time }}</span> phút
                        </div>
                    </div>
                    @endforeach
                    @if($routes->isEmpty())
                    <p class="text-xs text-slate-400 text-center py-4">Chưa có tuyến nào. Gom batch trước.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Auto Assign --}}
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="px-5 py-4 bg-gradient-to-r from-emerald-50 to-emerald-100/50 border-b border-emerald-100 flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white flex-shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-emerald-900">3. Tự Động Phân Công</h3>
                    <p class="text-[10px] text-emerald-600 font-medium">Auto-Assign — Heuristic Scoring</p>
                </div>
            </div>
            <div class="p-5">
                {{-- Shipper Status --}}
                <div class="space-y-2 mb-4">
                    @forelse($activeShippers as $s)
                    <div class="auto-assign-shipper-item flex items-center justify-between bg-emerald-50 border border-emerald-100 rounded-lg px-3 py-2 text-xs" data-region="{{ $s->region ?? '10.7769,106.7009' }}">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="font-bold text-slate-800">{{ $s->user->username ?? 'N/A' }}</span>
                            <span class="text-slate-400">{{ $s->vehicle_type }}</span>
                        </div>
                        <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-[10px] font-bold">{{ $s->active_order_count }} đơn</span>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 text-center py-2">Không có Shipper Online</p>
                    @endforelse
                </div>

                <form action="{{ route('admin.dispatch.auto-assign') }}" method="POST" id="autoAssignForm">
                    @csrf
                    <div class="mb-3">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Chọn tuyến chưa gán Shipper:</label>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto">
                            @foreach($routes->where('shipper_id', null) as $route)
                            <label class="auto-assign-route-item flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 cursor-pointer hover:border-emerald-300" 
                                data-lat="{{ optional(optional(optional($route->routeOrders->first())->order)->hub)->latitude ?? optional(optional($route->routeOrders->first())->order)->pickup_lat ?? '10.7769' }}" 
                                data-lng="{{ optional(optional(optional($route->routeOrders->first())->order)->hub)->longitude ?? optional(optional($route->routeOrders->first())->order)->pickup_lng ?? '106.7009' }}">
                                <input type="checkbox" name="route_ids[]" value="{{ $route->id }}" class="rounded text-emerald-600" checked>
                                <span class="text-xs font-bold text-slate-700">Tuyến #{{ $route->id }}</span>
                                <span class="text-[10px] text-slate-400">{{ $route->routeOrders->count() }} điểm</span>
                                <span class="ml-auto text-[10px] text-slate-400">{{ $route->total_distance }} km</span>
                            </label>
                            @endforeach
                            @if($routes->where('shipper_id', null)->isEmpty())
                            <p class="text-xs text-slate-400 text-center py-2">Không có tuyến nào chưa gán</p>
                            @endif
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-1.5" {{ $routes->where('shipper_id', null)->isEmpty() ? 'disabled' : '' }}>
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                        Tự Động Phân Công Shipper
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- PANEL 3: FULL ROUTE MAP --}}
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="px-5 py-4 bg-gradient-to-r from-slate-800 to-slate-900 border-b border-slate-700 flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-white/10 flex items-center justify-center text-white flex-shrink-0">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Bản Đồ Điều Phối</h3>
                    <p class="text-[10px] text-slate-400">Tất cả đơn & tuyến đường</p>
                </div>
            </div>
            <div class="flex gap-1.5 items-center flex-wrap">
            <div class="h-4 w-px bg-slate-700"></div>
                <button id="map-btn-orders" onclick="showAllOrders()" class="map-mode-btn px-2.5 py-1 text-[10px] font-bold rounded-lg bg-blue-500 text-white transition">Đơn hàng</button>
                <button id="map-btn-all" onclick="showAllRoutes()" class="map-mode-btn px-2.5 py-1 text-[10px] font-bold rounded-lg bg-slate-600 text-white hover:bg-violet-500 transition">Tất cả tuyến</button>
                <!-- Route filter pills (generated by JS after map init) -->
                <div id="route-filter-pills" class="flex gap-1 flex-wrap"></div>
            </div>
        </div>
        <div id="dispatchMap" class="h-[520px] w-full bg-slate-100 relative z-10">
        </div>
        <div class="px-4 py-3 bg-slate-50 border-t border-slate-100 flex flex-wrap items-center gap-4 text-[10px] text-slate-500">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>Đơn chưa gán</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-orange-400"></span>Đang giao</span>
            <span class="h-4 w-px bg-slate-200"></span>
            <span class="flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center rounded-full w-5 h-5 text-white font-black text-[8px] shadow" style="background:#6d28d9;border:2px solid white;">A★</span>
                Trạm Hub (mỗi tuyến 1 màu)
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center rounded-full w-5 h-5 text-white font-black text-[8px] shadow" style="background:#6d28d9;border:2px dashed rgba(255,255,255,0.9);">A↑</span>
                Điểm lấy hàng
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-flex items-center justify-center rounded-full w-5 h-5 text-white font-black text-[8px] shadow" style="background:#6d28d9;">A1</span>
                Điểm giao (theo thứ tự)
            </span>
            <span class="flex items-center gap-1.5"><span class="w-6 h-0.5 bg-violet-500"></span>Đường đi (OSRM)</span>
        </div>
    </div>
</div>
    
    {{-- PANEL 4: PHÂN TÍCH NÂNG CAO (3 THUẬT TOÁN) --}}
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden mb-6 mt-6">
        <div class="px-5 py-4 bg-gradient-to-r from-rose-50 to-orange-50 border-b border-rose-100 flex items-center gap-3">
            <div class="h-9 w-9 rounded-xl bg-rose-500 flex items-center justify-center text-white flex-shrink-0">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-bold text-rose-900">Phân Tích Nâng Cao</h3>
                <p class="text-[10px] text-rose-600 font-medium">Ưu tiên đơn, So sánh Shipper & Phân tích Tải trọng</p>
            </div>
        </div>
        <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100">
            
            {{-- Feature 1: Priority --}}
            <div class="px-2">
                <h4 class="text-xs font-bold text-slate-800 mb-2 flex items-center gap-1.5"><span class="w-2 h-2 bg-rose-500 rounded-full"></span> 1. Đơn Hàng Ưu Tiên (FIFO)</h4>
                <p class="text-[10px] text-slate-500 mb-3">Phân tích các đơn hàng chờ lâu nhất (ưu tiên thời gian tới trước).</p>
                <button onclick="loadPriorityOrders()" class="mb-3 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 text-[10px] font-bold rounded-lg border border-rose-200 transition">Tải danh sách ưu tiên</button>
                <div id="priority-results" class="space-y-2 max-h-60 overflow-y-auto pr-1"></div>
            </div>

            {{-- Feature 2: Shipper Comparison --}}
            <div class="px-2 md:px-4">
                <h4 class="text-xs font-bold text-slate-800 mb-2 flex items-center gap-1.5"><span class="w-2 h-2 bg-blue-500 rounded-full"></span> 2. So Sánh Top 5 Tài Xế</h4>
                <p class="text-[10px] text-slate-500 mb-3">Tìm ra 5 tài xế tối ưu nhất cho một tọa độ (Distance + Load).</p>

                <div class="flex gap-2 mb-3">
                    <input type="text" id="cmp-lat" placeholder="Vĩ độ" class="w-1/2 border border-slate-200 rounded-lg px-2 py-1 text-[10px]" value="10.7769">
                    <input type="text" id="cmp-lng" placeholder="Kinh độ" class="w-1/2 border border-slate-200 rounded-lg px-2 py-1 text-[10px]" value="106.7009">
                </div>
                <button onclick="loadCompareShippers()" class="mb-3 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 text-[10px] font-bold rounded-lg border border-blue-200 transition">Tìm tài xế tối ưu</button>
                <div id="compare-results" class="space-y-2 max-h-60 overflow-y-auto pr-1"></div>
            </div>

            {{-- Feature 3: Capacity --}}
            <div class="px-2 md:px-4">
                <h4 class="text-xs font-bold text-slate-800 mb-2 flex items-center gap-1.5"><span class="w-2 h-2 bg-emerald-500 rounded-full"></span> 3. Năng Lực Vận Chuyển</h4>
                <p class="text-[10px] text-slate-500 mb-3">Phân tích khả năng nhận thêm đơn (Max đơn & Max khối lượng) theo loại xe.</p>
                <button onclick="loadShipperCapacity()" class="mb-3 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-600 text-[10px] font-bold rounded-lg border border-emerald-200 transition">Kiểm tra năng lực tải</button>
                <div id="capacity-results" class="space-y-2 max-h-60 overflow-y-auto pr-1"></div>
            </div>

        </div>
    </div>

<script>
// ============ MAP SETUP ============
let dispatchMap, previewMap;
let dispatchLayers = [];
let previewLayers  = [];

const COLORS = ['#6d28d9','#2563eb','#059669','#d97706','#dc2626','#0891b2','#7c3aed','#be185d'];

const unassignedOrders = {!! json_encode($unassignedOrders->map(fn($o) => [
    'id' => $o->id, 'code' => $o->order_code,
    'lat' => (float)$o->delivery_lat, 'lng' => (float)$o->delivery_lng,
    'address' => $o->delivery_address,
])->values()) !!};

const routes = {!! json_encode($routes->map(function($r) {
    $routeOrders = $r->routeOrders->sortBy('stop_sequence');
    $points = [];
    
    // Starting Hub
    $firstRo = $routeOrders->first();
    if ($firstRo && $firstRo->order && $firstRo->order->hub && $firstRo->order->hub->latitude && $firstRo->order->hub->longitude) {
        $points[] = [
            'type' => 'hub',
            'name' => $firstRo->order->hub->name,
            'lat' => (float)$firstRo->order->hub->latitude,
            'lng' => (float)$firstRo->order->hub->longitude,
            'code' => 'HUB'
        ];
    }
    
    // Pickups and Deliveries in chronological order
    foreach ($routeOrders as $ro) {
        $ord = $ro->order;
        if ($ord && !in_array($ord->status, ['delivered', 'failed', 'cancelled'])) {
            if ($ord->pickup_lat && $ord->pickup_lng) {
                $points[] = [
                    'type' => 'pickup',
                    'name' => 'Nhận: ' . $ord->pickup_address,
                    'lat' => (float)$ord->pickup_lat,
                    'lng' => (float)$ord->pickup_lng,
                    'code' => $ord->order_code
                ];
            }
            if ($ord->delivery_lat && $ord->delivery_lng) {
                $points[] = [
                    'type' => 'delivery',
                    'name' => 'Giao: ' . $ord->delivery_address,
                    'lat' => (float)$ord->delivery_lat,
                    'lng' => (float)$ord->delivery_lng,
                    'code' => $ord->order_code
                ];
            }
        }
    }

    return [
        'id' => $r->id,
        'shipper' => $r->shipper->user->username ?? 'Chưa gán',
        'points' => $points,
        'stops' => $routeOrders->filter(function($ro) {
            return $ro->order && !in_array($ro->order->status, ['delivered', 'failed', 'cancelled']);
        })->map(fn($ro) => [
            'seq' => $ro->stop_sequence,
            'lat' => (float)($ro->order->delivery_lat ?? 0),
            'lng' => (float)($ro->order->delivery_lng ?? 0),
            'code' => $ro->order->order_code ?? '',
        ])->filter(fn($s) => $s['lat'] && $s['lng'])->values(),
    ];
})->values()) !!};

let currentRegionBase = [10.7769, 106.7009];
let currentViewMode = 'orders';

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

const createNumberedIcon = (number, type, color) => {
    return L.divIcon({
        className: 'custom-div-icon',
        html: `
            <div class="relative flex items-center justify-center w-6 h-6 rounded-full text-white font-black text-[9px] border border-white/80 shadow-md" style="background-color: ${color};">
                ${number}
                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-0 h-0 border-t-4 border-x-4 border-x-transparent" style="border-t-color: ${color};"></div>
            </div>
        `,
        iconSize: [24, 28],
        iconAnchor: [12, 28]
    });
};

function updateGuideHighlight() {
    const hasUnassigned = unassignedOrders.length > 0;
    const hasRoutes = routes.length > 0;
    const hasUnassignedRoutes = routes.some(r => !r.shipper_id);

    // Determine which step is active
    let activeStep = 1;
    if (!hasRoutes) {
        activeStep = 1;
    } else if (hasUnassignedRoutes) {
        activeStep = 2;
    } else {
        activeStep = 3;
    }

    const stepColors = { active: '#6366f1', done: '#10b981', pending: '#94a3b8' };
    const stepLabels = { active: '▶ Đang thực hiện', done: '✓ Hoàn thành', pending: '○ Chờ xử lý' };
    const stepBg = { active: 'bg-indigo-50', done: 'bg-emerald-50', pending: '' };

    for (let s = 1; s <= 3; s++) {
        const card = document.getElementById(`step-card-${s}`);
        const icon = document.getElementById(`step-icon-${s}`);
        const status = document.getElementById(`step-status-${s}`);
        if (!card || !icon || !status) continue;

        let state = 'pending';
        if (s < activeStep) state = 'done';
        else if (s === activeStep) state = 'active';

        // Reset card
        card.className = `p-4 flex items-start gap-3 transition-all ${state === 'active' ? 'bg-indigo-50/60' : state === 'done' ? 'bg-emerald-50/40' : ''}`;

        // Icon
        icon.className = 'h-9 w-9 rounded-xl flex items-center justify-center text-sm font-black flex-shrink-0 transition-all';
        if (state === 'active') {
            icon.style.cssText = 'background:#6366f1;color:white;box-shadow:0 0 0 3px #e0e7ff;';
            icon.textContent = s;
        } else if (state === 'done') {
            icon.style.cssText = 'background:#10b981;color:white;';
            icon.textContent = '✓';
        } else {
            icon.style.cssText = 'background:#f1f5f9;color:#94a3b8;';
            icon.textContent = s;
        }

        // Status badge
        const dotColor = state === 'active' ? 'bg-indigo-500' : state === 'done' ? 'bg-emerald-500' : 'bg-slate-300';
        const badgeBg = state === 'active' ? 'bg-indigo-100 text-indigo-700' : state === 'done' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-400';
        const labelText = state === 'active' ? '▶ Đang thực hiện' : state === 'done' ? '✓ Hoàn thành' : '○ Chờ';
        status.className = `mt-1.5 inline-flex items-center gap-1 text-[10px] font-bold rounded-full px-2 py-0.5 ${badgeBg}`;
        status.innerHTML = `<span class="w-1.5 h-1.5 rounded-full ${dotColor}"></span> ${labelText}`;
    }

    // Update badge in stepper header
    const badge = document.getElementById('stepper-step-badge');
    if (badge) {
        badge.textContent = `Bước ${activeStep}/3 đang cần thực hiện`;
    }
}

function toggleStepper() {
    const body = document.getElementById('stepper-body');
    const chevron = document.getElementById('stepper-chevron');
    if (body && chevron) {
        body.classList.toggle('hidden');
        chevron.style.transform = body.classList.contains('hidden') ? 'rotate(180deg)' : '';
    }
}

function initMaps() {
    // Main dispatch map
    dispatchMap = L.map('dispatchMap').setView([10.7769, 106.7009], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OSM' }).addTo(dispatchMap);

    // Preview map (small)
    previewMap = L.map('previewMap').setView([10.7769, 106.7009], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OSM' }).addTo(previewMap);

    showAllOrders();
    updatePreview();
    buildRouteFilterPills();
}

function clearLayers(map, layers) {
    layers.forEach(l => map.removeLayer(l));
    layers.length = 0;
}

function showAllOrders() {
    currentViewMode = 'orders';
    hideRouteInfoPanel();
    setMapModeActive('map-btn-orders');
    // Reset pill styles
    document.querySelectorAll('#route-filter-pills button').forEach((b, i) => {
        const c = COLORS[i % COLORS.length];
        b.style.cssText = `background:${c}22;border:1.5px solid ${c};color:${c};`;
    });
    buildRouteFilterPills();
    clearLayers(dispatchMap, dispatchLayers);
    let bounds = [];
    
    // Define max radius in km to show on map for selected city
    const MAX_RADIUS_KM = 50;

    unassignedOrders.forEach(o => {
        if (calculateDistance(currentRegionBase[0], currentRegionBase[1], o.lat, o.lng) > MAX_RADIUS_KM) return;
        
        let m = L.circleMarker([o.lat, o.lng], { radius: 7, color: '#2563eb', fillColor: '#3b82f6', fillOpacity: 0.9, weight: 2 })
            .addTo(dispatchMap).bindPopup(`<b>${o.code}</b><br>${o.address}`);
        dispatchLayers.push(m);
        bounds.push([o.lat, o.lng]);
    });
    // Also show route stops in orange
    routes.forEach((route, ri) => {
        // Check if route is in the region (using the first stop as reference)
        if (route.stops.length > 0 && calculateDistance(currentRegionBase[0], currentRegionBase[1], route.stops[0].lat, route.stops[0].lng) > MAX_RADIUS_KM) return;

        route.stops.forEach(s => {
            let m = L.circleMarker([s.lat, s.lng], { radius: 6, color: '#d97706', fillColor: '#f59e0b', fillOpacity: 0.85, weight: 2 })
                .addTo(dispatchMap).bindPopup(`<b>Tuyến #${route.id} — ${route.shipper}</b><br>${s.code}`);
            dispatchLayers.push(m);
            bounds.push([s.lat, s.lng]);
        });
    });
    if (bounds.length) dispatchMap.fitBounds(bounds, { padding: [30, 30] });
    updateGuideHighlight();
}

function setMapModeActive(btnId) {
    document.querySelectorAll('.map-mode-btn').forEach(b => {
        b.classList.remove('bg-violet-500', 'bg-blue-500', 'ring-2', 'ring-white/30');
        b.classList.add('bg-slate-600');
    });
    const btn = document.getElementById(btnId);
    if (btn) {
        btn.classList.remove('bg-slate-600');
        btn.classList.add('bg-violet-500', 'ring-2', 'ring-white/30');
    }
}

function buildRouteFilterPills() {
    const container = document.getElementById('route-filter-pills');
    if (!container) return;
    container.innerHTML = '';
    const ROUTE_LETTERS = 'ABCDEFGHIJKLMNOP';
    routes.forEach((route, ri) => {
        const letter = ROUTE_LETTERS[ri % ROUTE_LETTERS.length];
        const color = COLORS[ri % COLORS.length];
        const pill = document.createElement('button');
        pill.id = `map-btn-route-${ri}`;
        pill.className = 'map-mode-btn px-2 py-1 text-[10px] font-black rounded-lg text-white transition bg-slate-600 hover:opacity-90';
        pill.style.cssText = `background:${color}33;border:1.5px solid ${color};color:${color};`;
        pill.textContent = `Tuyến ${letter}`;
        pill.title = `Chỉ xem Tuyến ${letter} — #${route.id} (${route.shipper || 'chưa gán'})`;
        pill.onclick = () => showFilteredRoute(ri);
        container.appendChild(pill);
    });
}

function showFilteredRoute(routeIndex) {
    const ROUTE_LETTERS = 'ABCDEFGHIJKLMNOP';
    const letter = ROUTE_LETTERS[routeIndex % ROUTE_LETTERS.length];
    setMapModeActive(`map-btn-route-${routeIndex}`);
    // Highlight active pill border
    document.querySelectorAll('#route-filter-pills button').forEach((b, i) => {
        const c = COLORS[i % COLORS.length];
        if (i === routeIndex) {
            b.style.cssText = `background:${c};border:2px solid white;color:white;box-shadow:0 0 0 2px ${c};`;
        } else {
            b.style.cssText = `background:${c}22;border:1.5px solid ${c};color:${c};`;
        }
    });
    currentViewMode = 'routes';
    clearLayers(dispatchMap, dispatchLayers);
    let bounds = [];
    const route = routes[routeIndex];
    const ri = routeIndex;
    const color = COLORS[ri % COLORS.length];
    const pts = route.points.map(p => [p.lat, p.lng]);
    if (pts.length > 1) {
        let coordsStr = pts.map(pt => `${pt[1]},${pt[0]}`).join(';');
        let osrmUrl = `https://router.project-osrm.org/route/v1/driving/${coordsStr}?overview=full&geometries=geojson`;
        fetch(osrmUrl)
            .then(res => res.json())
            .then(data => {
                if (currentViewMode !== 'routes') return;
                if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                    let routeLine = L.geoJSON(data.routes[0].geometry, { style: { color, weight: 5, opacity: 0.9 } }).addTo(dispatchMap);
                    dispatchLayers.push(routeLine);
                } else {
                    let line = L.polyline(pts, { color, weight: 5, opacity: 0.9, dashArray: '8,6' }).addTo(dispatchMap);
                    dispatchLayers.push(line);
                }
            })
            .catch(() => {
                if (currentViewMode !== 'routes') return;
                let line = L.polyline(pts, { color, weight: 5, opacity: 0.9, dashArray: '8,6' }).addTo(dispatchMap);
                dispatchLayers.push(line);
            });
    }
    renderRouteMarkers(route, ri, bounds, letter, color);
    if (bounds.length) dispatchMap.fitBounds(bounds, { padding: [50, 50] });
    updateGuideHighlight();

    // Show info panel below map
    showRouteInfoPanel(route, ri, letter, color);
}

function showRouteInfoPanel(route, ri, letter, color) {
    let panel = document.getElementById('route-info-panel');
    if (!panel) {
        panel = document.createElement('div');
        panel.id = 'route-info-panel';
        panel.className = 'px-4 py-3 border-t text-[11px] flex items-center gap-4 flex-wrap';
        document.getElementById('dispatchMap').after(panel);
    }
    const stops = route.points.filter(p => p.type === 'delivery');
    panel.style.borderColor = color + '40';
    panel.style.background = color + '08';
    panel.innerHTML = `
        <span style="font-weight:900;color:${color};font-size:13px;">Tuyến ${letter}</span>
        <span class="text-slate-500">ID: #${route.id}</span>
        <span class="text-slate-500 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg> Shipper: <b>${route.shipper || 'Chưa gán'}</b></span>
        <span class="text-slate-500 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg> ${stops.length} điểm giao</span>
        ${route.total_km ? `<span class="text-slate-500 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg> ${route.total_km} km</span>` : ''}
        ${route.estimated_time ? `<span class="text-slate-500 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> ~${route.estimated_time} phút</span>` : ''}
        <button onclick="hideRouteInfoPanel()" class="ml-auto text-slate-400 hover:text-slate-600 text-xs px-2 py-0.5 rounded hover:bg-slate-100 flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg> Đóng</button>
    `;
    panel.classList.remove('hidden');
}

function hideRouteInfoPanel() {
    const p = document.getElementById('route-info-panel');
    if (p) p.classList.add('hidden');
}

function renderRouteMarkers(route, ri, bounds, routeLetter, color) {
    let deliveryCount = 0;
    route.points.forEach((p, idx) => {
        let markerColor = color;
        let label = '';
        let borderStyle = '2px solid rgba(255,255,255,0.8)';
        if (p.type === 'hub') {
            label = routeLetter + '★';
            borderStyle = '2px solid white';
        } else if (p.type === 'pickup') {
            label = routeLetter + '↑';
            borderStyle = '2px dashed rgba(255,255,255,0.9)';
        } else {
            deliveryCount++;
            label = routeLetter + deliveryCount;
        }
        let icon = L.divIcon({
            className: '',
            html: `<div style="background:${markerColor};border:${borderStyle};border-radius:50%;width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-size:8px;font-weight:900;color:white;box-shadow:0 2px 6px rgba(0,0,0,0.35);position:relative;white-space:nowrap;">${label}<div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);width:0;height:0;border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid ${markerColor};"></div></div>`,
            iconSize: [26, 32],
            iconAnchor: [13, 32]
        });
        const typeLabel = p.type === 'hub' ? '<svg class="w-3 h-3 inline-block -mt-0.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg> Trạm xuất phát' : p.type === 'pickup' ? '<svg class="w-3 h-3 inline-block -mt-0.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg> Lấy hàng' : `<svg class="w-3 h-3 inline-block -mt-0.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg> Điểm giao #${deliveryCount}`;
        let m = L.marker([p.lat, p.lng], { icon })
            .addTo(dispatchMap)
            .bindPopup(`<div style="font-size:11px;min-width:160px;"><div style="font-weight:700;color:${color};margin-bottom:4px;">Tuyến ${routeLetter} #${route.id}</div><div><b>${typeLabel}</b></div><div style="color:#555;margin-top:2px;">${p.name}</div>${p.code ? `<div style="color:#888;font-size:10px;margin-top:2px;">Mã: ${p.code}</div>` : ''}${route.shipper ? `<div style="color:#888;font-size:10px;">Shipper: ${route.shipper}</div>` : ''}</div>`);
        dispatchLayers.push(m);
        bounds.push([p.lat, p.lng]);
    });
}

function showAllRoutes(skipActive) {
    currentViewMode = 'routes';
    hideRouteInfoPanel();
    if (!skipActive) setMapModeActive('map-btn-all');
    // Reset pill styles
    document.querySelectorAll('#route-filter-pills button').forEach((b, i) => {
        const c = COLORS[i % COLORS.length];
        b.style.cssText = `background:${c}22;border:1.5px solid ${c};color:${c};`;
    });
    clearLayers(dispatchMap, dispatchLayers);
    let bounds = [];
    
    routes.forEach((route, ri) => {
        const color = COLORS[ri % COLORS.length];
        const pts = route.points.map(p => [p.lat, p.lng]);
        if (pts.length > 1) {
            let coordsStr = pts.map(pt => `${pt[1]},${pt[0]}`).join(';');
            let osrmUrl = `https://router.project-osrm.org/route/v1/driving/${coordsStr}?overview=full&geometries=geojson`;
            
            fetch(osrmUrl)
                .then(res => res.json())
                .then(data => {
                    if (currentViewMode !== 'routes') return;
                    if (data.code === 'Ok' && data.routes && data.routes.length > 0) {
                        let routeGeometry = data.routes[0].geometry;
                        let routeLine = L.geoJSON(routeGeometry, {
                            style: {
                                color,
                                weight: 4,
                                opacity: 0.85
                            }
                        }).addTo(dispatchMap);
                        dispatchLayers.push(routeLine);
                    } else {
                        let line = L.polyline(pts, { color, weight: 4, opacity: 0.85, dashArray: '8,6' }).addTo(dispatchMap);
                        dispatchLayers.push(line);
                    }
                })
                .catch(() => {
                    if (currentViewMode !== 'routes') return;
                    let line = L.polyline(pts, { color, weight: 4, opacity: 0.85, dashArray: '8,6' }).addTo(dispatchMap);
                    dispatchLayers.push(line);
                });
        }
        const ROUTE_LETTERS = 'ABCDEFGHIJKLMNOP';
        const routeLetter = ROUTE_LETTERS[ri % ROUTE_LETTERS.length];
        renderRouteMarkers(route, ri, bounds, routeLetter, color);
    });
    if (bounds.length) dispatchMap.fitBounds(bounds, { padding: [30, 30] });
    updateGuideHighlight();
}

function updatePreview() {
    clearLayers(previewMap, previewLayers);
    const radius = parseFloat(document.getElementById('radiusKm').value) || 5;
    const max    = parseInt(document.getElementById('maxOrders').value) || 5;

    fetch(`/admin/dispatch/preview-batch?radius_km=${radius}&max_orders=${max}`)
        .then(r => r.json())
        .then(data => {
            let bounds = [];
            data.orders_map.forEach(o => {
                let m = L.circleMarker([o.lat, o.lng], { radius: 6, color: '#2563eb', fillColor: '#3b82f6', fillOpacity: 0.85, weight: 2 })
                    .addTo(previewMap).bindPopup(`<b>${o.code}</b><br>${o.address}`);
                previewLayers.push(m);
                bounds.push([o.lat, o.lng]);
            });
            // Draw batch circles
            data.batches.forEach((b, bi) => {
                if (b.count >= 2) {
                    let c = L.circle([b.center.lat, b.center.lng], {
                        radius: radius * 1000,
                        color: COLORS[bi % COLORS.length],
                        fillColor: COLORS[bi % COLORS.length],
                        fillOpacity: 0.08,
                        weight: 1.5,
                        dashArray: '4,4'
                    }).addTo(previewMap);
                    previewLayers.push(c);
                }
            });
            if (bounds.length) previewMap.fitBounds(bounds, { padding: [20, 20] });

            const el = document.getElementById('previewResult');
            el.classList.remove('hidden');
            el.innerHTML = `Dự kiến tạo <b>${data.total_batches}</b> batch từ <b>${data.total_orders}</b> đơn (bán kính ${radius} km)`;
        }).catch(() => {});
}

function handleOptimize(event, routeId, form) {
    event.preventDefault();
    
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = `<svg class="animate-spin h-3 w-3 mr-1 inline-block" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Tối ưu...`;

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        
        if (data.success) {
            // Update route card info
            const card = document.getElementById(`route-card-${routeId}`);
            if (card) {
                const distEl = card.querySelector('.route-dist-val');
                const timeEl = card.querySelector('.route-time-val');
                if (distEl) distEl.textContent = data.route.total_distance;
                if (timeEl) timeEl.textContent = data.route.estimated_time;
            }
            
            // Update JS routes array
            const routeIdx = routes.findIndex(r => r.id === routeId);
            if (routeIdx !== -1) {
                routes[routeIdx] = data.route;
            }
            
            // Redraw map with the active mode
            if (currentViewMode === 'routes') {
                showAllRoutes();
            } else {
                showAllOrders();
            }
            
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Có lỗi xảy ra', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalText;
        showToast('Có lỗi kết nối hệ thống', 'error');
    });
}

function showToast(message, type = 'success') {
    const existing = document.getElementById('floating-toast');
    if (existing) existing.remove();
    
    const toast = document.createElement('div');
    toast.id = 'floating-toast';
    toast.className = `fixed bottom-5 right-5 z-[9999] px-5 py-3.5 rounded-xl shadow-2xl text-xs font-bold border transition-all duration-300 transform translate-y-10 opacity-0 flex items-center gap-2 ${
        type === 'success' 
            ? 'bg-emerald-50 text-emerald-800 border-emerald-200' 
            : 'bg-rose-50 text-rose-800 border-rose-200'
    }`;
    
    const icon = type === 'success' 
        ? `<svg class="h-4 w-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`
        : `<svg class="h-4 w-4 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`;
        
    toast.innerHTML = icon + `<span>${message}</span>`;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('translate-y-10', 'opacity-0');
    }, 10);
    
    setTimeout(() => {
        toast.classList.add('translate-y-10', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

function toggleGuidePanel() {
    const el = document.getElementById('steps-guide-panel');
    if (el) {
        el.classList.toggle('hidden');
    }
}

// ==========================================
// ADVANCED ANALYTICS FUNCTIONS
// ==========================================
function changeRegionFromTop() {
    const val = document.getElementById('top-region-select').value;
    const [lat, lng] = val.split(',');
    currentRegionBase = [parseFloat(lat), parseFloat(lng)];
    
    // Update input boxes in panel 4 if they exist
    const latInput = document.getElementById('cmp-lat');
    const lngInput = document.getElementById('cmp-lng');
    if(latInput) latInput.value = lat;
    if(lngInput) lngInput.value = lng;
    
    // Pan the maps to the new region gracefully
    if (dispatchMap) dispatchMap.flyTo([lat, lng], 12, { duration: 1.5 });
    if (previewMap) previewMap.flyTo([lat, lng], 12, { duration: 1.5 });
    
    // Re-render map points to filter by new region
    if (currentViewMode === 'orders') showAllOrders();
    else showAllRoutes();
    
    // Auto refresh the analytics for the new region if they were already loaded
    const priorityContainer = document.getElementById('priority-results');
    if (priorityContainer && priorityContainer.innerHTML !== '') loadPriorityOrders();
    
    const capacityContainer = document.getElementById('capacity-results');
    if (capacityContainer && capacityContainer.innerHTML !== '') loadShipperCapacity();

    // Filter Auto Assign Shippers
    document.querySelectorAll('.auto-assign-shipper-item').forEach(el => {
        if (el.dataset.region === val) {
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    });

    // Filter Auto Assign Routes
    document.querySelectorAll('.auto-assign-route-item').forEach(el => {
        const rLat = parseFloat(el.dataset.lat);
        const rLng = parseFloat(el.dataset.lng);
        if (!isNaN(rLat) && !isNaN(rLng) && calculateDistance(currentRegionBase[0], currentRegionBase[1], rLat, rLng) <= 50) {
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    });
}

function loadPriorityOrders() {
    const container = document.getElementById('priority-results');
    container.innerHTML = `<p class="text-[10px] text-slate-400">Đang tải...</p>`;
    const lat = currentRegionBase[0];
    const lng = currentRegionBase[1];
    
    fetch(`/admin/dispatch/api/priority-orders?lat=${lat}&lng=${lng}`)
        .then(r => r.json())
        .then(data => {
            if(!data.success || data.data.length === 0) {
                container.innerHTML = `<p class="text-[10px] text-slate-400">Không có đơn hàng nào chờ xử lý.</p>`;
                return;
            }
            let html = '';
            data.data.forEach((o, index) => {
                const color = index === 0 ? 'rose' : (index < 3 ? 'orange' : 'slate');
                html += `
                    <div class="bg-${color}-50 border border-${color}-100 rounded-lg p-2 relative">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-[11px] font-bold text-${color}-700">${o.code}</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-${color}-200 text-${color}-800">Điểm: ${o.priority_score}</span>
                        </div>
                        <p class="text-[9px] text-slate-500 mb-1 truncate">${o.address}</p>
                        <p class="text-[9px] text-slate-600">Đã chờ: <b class="text-${color}-600">${o.wait_time_mins} phút</b> (${o.created_at})</p>
                    </div>
                `;
            });
            container.innerHTML = html;
        }).catch(e => {
            container.innerHTML = `<p class="text-[10px] text-red-500">Lỗi kết nối</p>`;
        });
}

function loadCompareShippers() {
    const container = document.getElementById('compare-results');
    const lat = document.getElementById('cmp-lat').value;
    const lng = document.getElementById('cmp-lng').value;
    container.innerHTML = `<p class="text-[10px] text-slate-400">Đang tìm tài xế...</p>`;
    
    fetch(`{{ route("admin.dispatch.compare-shippers") }}?lat=${lat}&lng=${lng}`)
        .then(r => r.json())
        .then(data => {
            if(!data.success) {
                container.innerHTML = `<p class="text-[10px] text-slate-400">${data.message || 'Không tìm thấy tài xế.'}</p>`;
                return;
            }
            let html = '';
            data.data.forEach((s, index) => {
                const rank = index + 1;
                const bg = rank === 1 ? 'bg-blue-100 border-blue-200' : 'bg-slate-50 border-slate-100';
                const text = rank === 1 ? 'text-blue-800' : 'text-slate-700';
                html += `
                    <div class="border rounded-lg p-2 ${bg}">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-[11px] font-bold ${text}">#${rank} ${s.name}</span>
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-blue-500 text-white shadow-sm">Score: ${s.score}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-1 text-[9px] text-slate-600 mt-1.5">
                            <div>Khoảng cách: <b>${s.distance_km} km</b></div>
                            <div>Đang giao: <b>${s.current_load} đơn</b></div>
                            <div class="col-span-2">Phương tiện: ${s.vehicle || 'N/A'}</div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }).catch(e => {
            container.innerHTML = `<p class="text-[10px] text-red-500">Lỗi kết nối</p>`;
        });
}

function loadShipperCapacity() {
    const container = document.getElementById('capacity-results');
    container.innerHTML = `<p class="text-[10px] text-slate-400">Đang tải...</p>`;
    const lat = currentRegionBase[0];
    const lng = currentRegionBase[1];
    
    fetch(`/admin/dispatch/api/shipper-capacity?lat=${lat}&lng=${lng}`)
        .then(r => r.json())
        .then(data => {
            if(!data.success || data.data.length === 0) {
                container.innerHTML = `<p class="text-[10px] text-slate-400">Không có dữ liệu tài xế.</p>`;
                return;
            }
            let html = '';
            data.data.forEach((s) => {
                const border = s.overloaded ? 'border-red-200 bg-red-50' : 'border-emerald-100 bg-emerald-50';
                const textColor = s.overloaded ? 'text-red-700' : 'text-emerald-700';
                
                html += `
                    <div class="border rounded-lg p-2 ${border}">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="text-[11px] font-bold ${textColor}">${s.name} <span class="font-normal text-[9px] text-slate-500">(${s.status})</span></span>
                            ${s.overloaded ? '<span class="text-[9px] bg-red-500 text-white px-1 py-0.5 rounded font-bold animate-pulse">Quá tải</span>' : '<span class="text-[9px] bg-emerald-500 text-white px-1 py-0.5 rounded font-bold">An toàn</span>'}
                        </div>
                        <div class="text-[9px] text-slate-600 space-y-1">
                            <div class="flex justify-between">
                                <span>Xe: <b>${s.vehicle}</b></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-10">Đơn:</span> 
                                <div class="flex-1 bg-white h-1.5 rounded-full overflow-hidden border border-slate-200">
                                    <div class="h-full ${s.overloaded ? 'bg-red-500' : 'bg-emerald-500'}" style="width: ${Math.min(100, (s.current_orders / s.max_orders) * 100)}%"></div>
                                </div>
                                <span class="w-16 text-right">${s.current_orders} / ${s.max_orders}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-10">Tải (kg):</span> 
                                <div class="flex-1 bg-white h-1.5 rounded-full overflow-hidden border border-slate-200">
                                    <div class="h-full ${s.current_weight > s.max_weight ? 'bg-red-500' : 'bg-emerald-500'}" style="width: ${Math.min(100, (s.current_weight / s.max_weight) * 100)}%"></div>
                                </div>
                                <span class="w-16 text-right">${s.current_weight} / ${s.max_weight}</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        }).catch(e => {
            container.innerHTML = `<p class="text-[10px] text-red-500">Lỗi kết nối</p>`;
        });
}

document.addEventListener('DOMContentLoaded', () => {
    initMaps();
    const initRegion = document.getElementById('top-region-select').value;
    
    // Filter Auto Assign Shippers
    document.querySelectorAll('.auto-assign-shipper-item').forEach(el => {
        if (el.dataset.region === initRegion) {
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    });

    // Filter Auto Assign Routes
    const [initLat, initLng] = initRegion.split(',');
    document.querySelectorAll('.auto-assign-route-item').forEach(el => {
        const rLat = parseFloat(el.dataset.lat);
        const rLng = parseFloat(el.dataset.lng);
        if (!isNaN(rLat) && !isNaN(rLng) && calculateDistance(parseFloat(initLat), parseFloat(initLng), rLat, rLng) <= 50) {
            el.style.display = 'flex';
        } else {
            el.style.display = 'none';
        }
    });
});
</script>
@endsection
