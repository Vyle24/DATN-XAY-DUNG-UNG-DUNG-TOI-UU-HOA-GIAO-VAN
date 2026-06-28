const fs = require('fs');
const file = 'c:/Users/Admin/Desktop/toi_uu_giao_van/resources/views/shipper/dashboard.blade.php';
let content = fs.readFileSync(file, 'utf8');

// Replace deliveringOrders block
content = content.replace(/@forelse\(\$deliveringOrders as \$order\)/g, '@forelse($deliveringSingleOrders as $order)');

// Inject active route banner before the grid
content = content.replace(/<div class=\"grid grid-cols-1 lg:grid-cols-2 gap-4\">/g, function(match, offset, str) {
    if (offset < 4000) { 
        return `        @if($activeRoute)
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">`;
    }
    return match;
});

// Update the empty state for deliveringOrders
content = content.replace(/@empty\s*<div class=\"col-span-2 bg-white rounded-2xl p-8 border border-slate-200\/60 shadow-sm text-center\">[\s\S]*?<\/div>\s*@endforelse/g, function(match, offset, str) {
    if (offset < 6000) { 
        return `@empty
                @if(!$activeRoute)
                <div class="col-span-2 bg-white rounded-2xl p-8 border border-slate-200/60 shadow-sm text-center">
                    <div class="h-12 w-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                        <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 01-4 0m12 0a2 2 0 11-4 0 2 2 0 01-4 0m9-1v-4a2 2 0 00-2-2h-3V8a1 1 0 00-1-1H4a1 1 0 00-1 1v8h12m4 0h2a2 2 0 002-2v-3a2 2 0 00-2-2h-3v7m1.5-9V4a1 1 0 011-1h2a1 1 0 011 1v3" /></svg>
                    </div>
                    <h4 class="font-bold text-slate-800 text-sm">Chưa có đơn hàng cần giao</h4>
                    <p class="text-xs text-slate-400 mt-1">Các đơn hàng được phân phối sẽ hiển thị ở đây để bạn bắt đầu hành trình giao nhận.</p>
                </div>
                @endif
            @endforelse`;
    }
    return match;
});

// Replace assignedOrders loop header with assignedRoutes loop
content = content.replace(/@forelse\(\$assignedOrders as \$order\)/g, `            <!-- Assigned Routes -->
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
            @forelse($assignedSingleOrders as $order)`);

// Update empty state for assignedOrders
content = content.replace(/@empty\s*<div class=\"col-span-2 bg-white rounded-2xl p-8 border border-slate-200\/60 shadow-sm text-center\">[\s\S]*?<\/div>\s*@endforelse/g, function(match, offset, str) {
    if (offset > 6000) { 
        return `@empty
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
            @endforelse`;
    }
    return match;
});

// Update the count texts
content = content.replace('{{ $deliveringOrders->count() }}', '{{ $deliveringSingleOrders->count() }}');
content = content.replace('{{ $assignedOrders->count() }}', '{{ $assignedSingleOrders->count() + $assignedRoutes->count() }}');

fs.writeFileSync(file, content);
console.log("Dashboard updated");
