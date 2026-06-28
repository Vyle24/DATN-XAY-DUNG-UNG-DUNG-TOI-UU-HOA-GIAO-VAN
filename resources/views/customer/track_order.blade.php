@extends('layouts.customer')

@section('header_title', 'Theo Dõi Đơn Hàng #' . $order->order_code)

@section('content')
<div class="mb-4">
    <a href="{{ route('customer.dashboard') }}" class="inline-flex items-center text-xs font-bold text-slate-500 hover:text-slate-800 transition">
        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" /></svg>
        Quay lại bảng điều khiển
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left Details Block -->
    <div class="lg:col-span-5 space-y-6">
        <!-- Order Stats Info Card -->
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-5 md:p-6">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                <h3 class="font-extrabold text-slate-800 text-sm">Thông tin đơn hàng</h3>
                @if($order->status === 'pending')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Chờ xử lý</span>
                @elseif($order->status === 'processing')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">Đang giao</span>
                @elseif($order->status === 'delivered')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Thành công</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">Thất bại</span>
                @endif
            </div>

            <div class="space-y-3 text-xs text-slate-655">
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Mã đơn hàng:</span>
                    <span class="font-bold text-slate-800">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Ngày đặt:</span>
                    <span class="font-semibold text-slate-700">{{ $order->created_at->format('d/m/Y H:i:s') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Trọng lượng:</span>
                    <span class="font-semibold text-slate-700">{{ $order->total_weight }} kg</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Phí giao hàng:</span>
                    <span class="font-bold text-blue-600">{{ number_format($order->shipping_fee) }}đ</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Thanh toán:</span>
                    <span class="font-semibold text-slate-700">{{ $order->payment_method ?? 'COD' }}</span>
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Địa chỉ lấy hàng (Hub):</span>
                    <span class="font-semibold text-slate-800 block">{{ $order->pickup_address }}</span>
                </div>
                <div class="pt-2 border-t border-slate-100">
                    <span class="text-slate-400 font-medium block mb-1">Địa chỉ nhận hàng:</span>
                    <span class="font-semibold text-slate-800 block">{{ $order->delivery_address }}</span>
                </div>
            </div>
        </div>

        <!-- Assigned Shipper Info -->
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-5 md:p-6">
            <h4 class="font-bold text-slate-800 text-sm mb-4 pb-2 border-b border-slate-100">Thông tin Shipper</h4>
            @if($order->shipper)
            <div class="flex items-center space-x-4">
                <span class="h-12 w-12 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 flex items-center justify-center font-extrabold text-lg flex-shrink-0">
                    {{ substr($order->shipper->user->username ?? 'S', 0, 1) }}
                </span>
                <div class="flex-grow">
                    <h5 class="font-bold text-slate-800 text-xs">{{ $order->shipper->user->username ?? 'Chưa xác định' }}</h5>
                    <p class="text-[10px] text-slate-400 mt-0.5">Số điện thoại: {{ $order->shipper->user->phone ?? 'Không có' }}</p>
                    <div class="mt-2 flex items-center gap-1.5">
                        <span class="bg-indigo-50 border border-indigo-100 text-indigo-700 px-2 py-0.5 rounded text-[9px] font-bold">{{ $order->shipper->vehicle_type }}</span>
                        <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[9px] font-mono font-semibold">{{ $order->shipper->license_no }}</span>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center py-4 text-slate-400">
                <div class="h-10 w-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-2 animate-pulse">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <p class="text-xs font-semibold text-slate-500">Đang chờ hệ thống điều phối shipper</p>
                <p class="text-[10px] text-slate-400 mt-0.5">Đơn hàng của bạn sẽ được gán trong thời gian ngắn nhất.</p>
            </div>
            @endif
        </div>

        <!-- Tracking Logs Timeline -->
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-5 md:p-6">
            <h4 class="font-bold text-slate-800 text-sm mb-4 pb-2 border-b border-slate-100">Nhật ký vận đơn</h4>
            
            <div class="relative pl-6 border-l border-slate-200 space-y-6">
                @forelse($order->trackingLogs as $log)
                <div class="relative">
                    <!-- marker point -->
                    <span class="absolute -left-[30px] top-0.5 w-3 h-3 rounded-full border-2 border-white shadow-sm {{ $loop->first ? 'bg-blue-600 ring-4 ring-blue-100 animate-pulse' : 'bg-slate-300' }}"></span>
                    
                    <span class="text-[10px] font-bold text-slate-400 block">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                    <h5 class="text-xs font-bold text-slate-800 mt-0.5">
                        @if($log->status_at_time === 'created')
                            Đơn hàng được khởi tạo thành công
                        @elseif($log->status_at_time === 'assigned')
                            Đã gán cho Shipper
                        @elseif($log->status_at_time === 'accepted')
                            Shipper nhận đơn hàng
                        @elseif($log->status_at_time === 'processing')
                            Đang lấy hàng / Đang di chuyển
                        @elseif($log->status_at_time === 'delivered')
                            Giao hàng thành công
                        @elseif($log->status_at_time === 'failed')
                            Giao hàng thất bại / Trả lại
                        @else
                            {{ $log->status_at_time }}
                        @endif
                    </h5>
                    <p class="text-[10px] text-slate-500 mt-1 font-medium">{{ $log->location_name }}</p>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-2">Không có nhật ký hành trình.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Map Block -->
    <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 flex flex-col h-[400px] lg:h-[650px] sticky top-20">
        <h4 class="font-bold text-slate-800 text-sm mb-2">Hành Trình Giao Nhận Trực Quan</h4>
        <div id="trackingMap" class="flex-1 w-full rounded-xl overflow-hidden border border-slate-200/60"></div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const pickupLat = {{ (float)$order->pickup_lat }};
        const pickupLng = {{ (float)$order->pickup_lng }};
        const deliveryLat = {{ (float)$order->delivery_lat }};
        const deliveryLng = {{ (float)$order->delivery_lng }};
        
        let shipperLat = null;
        let shipperLng = null;

        @if($order->status === 'processing' && $order->shipper && $order->shipper->current_lat && $order->shipper->current_lng)
            shipperLat = {{ (float)$order->shipper->current_lat }};
            shipperLng = {{ (float)$order->shipper->current_lng }};
        @endif

        // Init map
        const map = L.map('trackingMap').setView([pickupLat, pickupLng], 14);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OSM'
        }).addTo(map);

        // Pickup Marker (Blue)
        const pickupMarker = L.marker([pickupLat, pickupLng], {
            icon: L.divIcon({
                className: '',
                html: `<div style="background:#3b82f6;border:2px dashed rgba(255,255,255,0.9);border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                           <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                           <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #3b82f6;"></div>
                       </div>`,
                iconSize: [32, 40],
                iconAnchor: [16, 40]
            })
        }).addTo(map).bindPopup("<b>Điểm lấy hàng (Hub)</b><br>{{ $order->pickup_address }}").openPopup();

        // Delivery Marker (Red)
        const deliveryMarker = L.marker([deliveryLat, deliveryLng], {
            icon: L.divIcon({
                className: '',
                html: `<div style="background:#ef4444;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                           <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                           <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #ef4444;"></div>
                       </div>`,
                iconSize: [32, 40],
                iconAnchor: [16, 40]
            })
        }).addTo(map).bindPopup("<b>Điểm giao nhận</b><br>{{ $order->delivery_address }}");

        // Shipper Marker (Orange/Moto) if available
        let polylinePoints = [];
        let groupLayers = [pickupMarker, deliveryMarker];
        let shipperMarker = null;

        if (shipperLat && shipperLng) {
            shipperMarker = L.marker([shipperLat, shipperLng], {
                icon: L.divIcon({
                    className: 'custom-shipper-marker',
                    html: `
                        <div class="relative flex items-center justify-center w-10 h-10 rounded-full bg-emerald-500 border-2 border-white shadow-lg text-lg text-white">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative">🛵</span>
                        </div>
                    `,
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                })
            }).addTo(map).bindPopup("<b>Vị trí Shipper: {{ $order->shipper->user->username ?? 'Shipper' }}</b><br>Đang di chuyển giao hàng...");
            
            groupLayers.push(shipperMarker);

            polylinePoints = [
                [pickupLat, pickupLng],
                [shipperLat, shipperLng],
                [deliveryLat, deliveryLng]
            ];
        } else {
            polylinePoints = [
                [pickupLat, pickupLng],
                [deliveryLat, deliveryLng]
            ];
        }

        // Draw Route using OSRM
        const coordinateString = polylinePoints.map(c => `${c[1]},${c[0]}`).join(';');
        const url = `https://router.project-osrm.org/route/v1/driving/${coordinateString}?overview=full&geometries=geojson`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.routes && data.routes.length > 0) {
                    const routeGeoJSON = data.routes[0].geometry;

                    // Lớp viền trắng bên dưới (tạo hiệu ứng đẹp như Google Maps)
                    L.geoJSON(routeGeoJSON, {
                        style: {
                            color: '#ffffff',
                            weight: 10,
                            opacity: 0.55,
                            lineJoin: 'round',
                            lineCap: 'round'
                        }
                    }).addTo(map);

                    // Đường đi màu xanh chính
                    L.geoJSON(routeGeoJSON, {
                        style: {
                            color: '#2563eb',
                            weight: 6,
                            opacity: 0.95,
                            lineJoin: 'round',
                            lineCap: 'round'
                        }
                    }).addTo(map);

                    // Fit boundaries to show the entire route
                    const group = new L.featureGroup(groupLayers);
                    map.fitBounds(group.getBounds().pad(0.15));
                } else {
                    drawStraightLineFallback();
                }
            })
            .catch(err => {
                console.error("OSRM Routing API Error:", err);
                drawStraightLineFallback();
            });

        function drawStraightLineFallback() {
            // Viền trắng
            L.polyline(polylinePoints, {
                color: '#ffffff',
                weight: 9,
                opacity: 0.5,
                dashArray: '12, 10',
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(map);
            // Đường xanh đứt nét
            L.polyline(polylinePoints, {
                color: '#2563eb',
                weight: 5,
                opacity: 0.9,
                dashArray: '12, 10',
                lineJoin: 'round',
                lineCap: 'round'
            }).addTo(map);

            const group = new L.featureGroup(groupLayers);
            map.fitBounds(group.getBounds().pad(0.15));
        }
    });
</script>
@endsection
