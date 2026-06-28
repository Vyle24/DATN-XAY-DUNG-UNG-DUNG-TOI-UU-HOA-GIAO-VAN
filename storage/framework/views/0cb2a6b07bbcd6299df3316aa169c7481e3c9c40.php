

<?php $__env->startSection('header_title', 'Tối Ưu Tuyến Đường Giao Hàng'); ?>

<?php $__env->startSection('content'); ?>


<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200/60 p-4 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">Tổng tuyến đường</span>
        <span class="text-2xl font-extrabold text-slate-800 mt-1 block"><?php echo e($routes->count()); ?></span>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/60 p-4 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">Tổng điểm dừng</span>
        <span class="text-2xl font-extrabold text-blue-600 mt-1 block"><?php echo e($routes->sum(fn($r) => $r->routeOrders->count())); ?></span>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/60 p-4 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">Quãng đường TB</span>
        <span class="text-2xl font-extrabold text-emerald-600 mt-1 block"><?php echo e($routes->count() ? round($routes->avg('total_distance'), 1) : 0); ?> km</span>
    </div>
    <div class="bg-white rounded-2xl border border-slate-200/60 p-4 shadow-sm">
        <span class="text-[10px] font-bold uppercase text-slate-400 block">Thời gian TB</span>
        <span class="text-2xl font-extrabold text-orange-500 mt-1 block"><?php echo e($routes->count() ? round($routes->avg('estimated_time')) : 0); ?> phút</span>
    </div>
</div>


<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6">
    <div>
        <h3 class="text-sm font-bold text-slate-800">Danh sách tuyến đường tối ưu</h3>
        <p class="text-xs text-slate-400 mt-0.5">Quản lý, gán Shipper và xem bản đồ lộ trình</p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        <a href="<?php echo e(route('admin.dispatch')); ?>" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100 transition shadow-sm">
            ⚡ Sang Điều Phối Thông Minh
        </a>

        <button onclick="document.getElementById('createRouteModal').classList.remove('hidden')"
            class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 text-white hover:bg-blue-700 transition shadow-sm">
            + Tạo Tuyến Mới
        </button>
    </div>
</div>


<div class="flex border-b border-slate-200 mb-6 gap-2">
    <button onclick="filterRoutes('all')" id="tab-btn-all" class="route-tab-btn py-2 px-4 text-xs font-bold text-blue-600 border-b-2 border-blue-600 transition focus:outline-none">
        Tất cả tuyến (<span><?php echo e($routes->count()); ?></span>)
    </button>
    <button onclick="filterRoutes('unassigned')" id="tab-btn-unassigned" class="route-tab-btn py-2 px-4 text-xs font-bold text-slate-500 hover:text-slate-700 transition focus:outline-none">
        Chưa gán Shipper (<span><?php echo e($routes->filter(fn($r) => !$r->shipper_id)->count()); ?></span>)
    </button>
    <button onclick="filterRoutes('assigned')" id="tab-btn-assigned" class="route-tab-btn py-2 px-4 text-xs font-bold text-slate-500 hover:text-slate-700 transition focus:outline-none">
        Đã gán Shipper (<span><?php echo e($routes->filter(fn($r) => $r->shipper_id)->count()); ?></span>)
    </button>
</div>


<div class="space-y-6" id="routes-container">
    <?php $__empty_1 = true; $__currentLoopData = $routes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php
        $firstStop = $route->routeOrders->first();
        $hubData   = ($firstStop && $firstStop->order && $firstStop->order->hub) ? $firstStop->order->hub : null;
    ?>
    <div class="route-card bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden" data-assigned="<?php echo e($route->shipper_id ? 'true' : 'false'); ?>">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-150 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center space-x-3">
                <div class="h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold border border-blue-100"><?php echo e($route->id); ?></div>
                <div>
                    <h4 class="text-sm font-bold text-slate-900">Tuyến đường #<?php echo e($route->id); ?></h4>
                    <p class="text-xs text-slate-400">Shipper: <span class="font-bold <?php echo e($route->shipper ? 'text-emerald-700' : 'text-rose-500'); ?>">
                        <?php echo e($route->shipper->user->username ?? '⚠ Chưa gán shipper'); ?>

                    </span></p>
                </div>
                <?php if(!$route->shipper): ?>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">Cần phân công</span>
                <?php else: ?>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Đã gán</span>
                <?php endif; ?>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <div class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-center">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Quãng đường</span>
                    <span class="font-extrabold text-slate-800"><?php echo e($route->total_distance); ?> km</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-center">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Ước tính</span>
                    <span class="font-extrabold text-slate-800"><?php echo e($route->estimated_time); ?> phút</span>
                </div>
                <div class="bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-center">
                    <span class="text-[9px] uppercase font-bold text-slate-400 block">Điểm dừng</span>
                    <span class="font-extrabold text-blue-600"><?php echo e($route->routeOrders->count()); ?></span>
                </div>
                <button onclick="openRouteMapModal(<?php echo e($route->id); ?>, <?php echo e(json_encode($hubData)); ?>, <?php echo e(json_encode($route->routeOrders)); ?>)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" /></svg>
                    Xem Map
                </button>
                <button onclick="openAssignModal(<?php echo e($route->id); ?>, '<?php echo e($route->shipper->user->username ?? 'Chua gan'); ?>')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition">
                    <svg class="h-3.5 w-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    Gán Shipper
                </button>
                <button onclick="openEditRouteModal(<?php echo e($route->id); ?>, <?php echo e($route->total_distance); ?>, <?php echo e($route->estimated_time); ?>)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold rounded-lg hover:bg-amber-100 transition">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Sửa
                </button>
                <form action="<?php echo e(route('admin.routes.destroy', $route->id)); ?>" method="POST"
                    onsubmit="return confirm('Xóa tuyến #<?php echo e($route->id); ?>? Tất cả đơn hàng sẽ bị bỏ gán Shipper.')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 border border-rose-200 text-xs font-bold rounded-lg hover:bg-rose-100 transition">
                        <svg class="h-3.5 w-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                        Xóa
                    </button>
                </form>
            </div>

        </div>

        <div class="p-6">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 block">Thứ tự điểm dừng:</span>
            <div class="relative pl-6 space-y-4">
                <div class="absolute left-[9px] top-2 bottom-2 w-0.5 bg-slate-200"></div>
                <?php $__empty_2 = true; $__currentLoopData = $route->routeOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                <div class="relative flex items-start text-xs">
                    <span class="absolute left-[-22px] top-2 h-3 w-3 rounded-full bg-white border-2 border-blue-500 ring-4 ring-blue-50"></span>
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                #<?php echo e($stop->stop_sequence); ?>

                            </span>
                        </div>
                        <div class="md:col-span-3">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-slate-800"><?php echo e($stop->order->order_code ?? 'N/A'); ?></span>
                                <span class="text-slate-400">•</span>
                                <span class="text-slate-500"><?php echo e($stop->order->customer->user->username ?? 'N/A'); ?></span>
                            </div>
                            <div class="space-y-0.5 text-slate-500">
                                <div class="flex items-center"><span class="w-1.5 h-1.5 bg-blue-400 rounded-full mr-1.5"></span> <?php echo e($stop->order->pickup_address ?? 'N/A'); ?></div>
                                <div class="flex items-center"><span class="w-1.5 h-1.5 bg-emerald-400 rounded-full mr-1.5"></span> <?php echo e($stop->order->delivery_address ?? 'N/A'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                <p class="text-xs text-slate-400 italic">Chưa có điểm dừng.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="bg-white rounded-2xl p-12 border border-slate-200/60 shadow-sm text-center">
        <div class="h-16 w-16 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4">
            <svg class="h-8 w-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" /></svg>
        </div>
        <h3 class="text-sm font-bold text-slate-800">Chưa có tuyến đường nào</h3>
        <p class="text-xs text-slate-400 mt-1 mb-4">Tạo tuyến mới hoặc dùng nút "Tạo Dữ Liệu Demo" để thử nghiệm.</p>
        <button onclick="document.getElementById('createRouteModal').classList.remove('hidden')"
            class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition">
            + Tạo Tuyến Đầu Tiên
        </button>
    </div>
    <?php endif; ?>
</div>




<div id="createRouteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Tạo Tuyến Đường Mới</h3>
            <button onclick="document.getElementById('createRouteModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form action="<?php echo e(route('admin.routes.store')); ?>" method="POST" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Shipper phụ trách *</label>
                <select name="shipper_id" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Chọn Shipper --</option>
                    <?php $__currentLoopData = \App\Models\Shipper::with('user')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>"><?php echo e($s->user->username ?? 'N/A'); ?> (<?php echo e($s->vehicle_type); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Đơn hàng cần giao (chọn nhiều) *</label>
                <select name="order_ids[]" multiple required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 h-36 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <?php $__currentLoopData = \App\Models\Order::whereIn('status', ['pending'])->where('assignment_status', 'unassigned')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($o->id); ?>"><?php echo e($o->order_code); ?> – <?php echo e(Str::limit($o->delivery_address, 40)); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Giữ Ctrl/Cmd để chọn nhiều đơn hàng</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Quãng đường (km)</label>
                    <input type="number" name="total_distance" step="0.1" min="0" value="10.0" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Thời gian ước tính (phút)</label>
                    <input type="number" name="estimated_time" min="1" value="45" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('createRouteModal').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700">Tạo Tuyến</button>
            </div>
        </form>
    </div>
</div>


<div id="assignShipperModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Gán Shipper vào Tuyến</h3>
            <button onclick="document.getElementById('assignShipperModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="assignShipperForm" method="POST" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <div class="text-xs text-slate-500 bg-slate-50 rounded-xl p-3 border border-slate-200">
                Tuyến đường: <span id="assignRouteLabel" class="font-bold text-slate-800"></span>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Chọn Shipper mới *</label>
                <select name="shipper_id" required class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Chọn Shipper --</option>
                    <?php $__currentLoopData = \App\Models\Shipper::with('user')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>"><?php echo e($s->user->username ?? 'N/A'); ?> (<?php echo e($s->vehicle_type); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('assignShipperModal').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl text-white bg-emerald-600 hover:bg-emerald-700">Xác nhận gán</button>
            </div>
        </form>
    </div>
</div>


<div id="routeMapModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-4xl shadow-2xl border border-slate-100 flex flex-col max-h-[92vh]">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 id="modal_route_title" class="text-sm font-bold text-slate-800">Bản Đồ Lộ Trình</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Đường đi ngắn nhất theo thứ tự tối ưu (OSRM Road Routing)</p>
            </div>
            <button onclick="closeRouteMapModal()" class="text-slate-400 hover:text-slate-700">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-5 overflow-y-auto flex-grow space-y-4">
            <div id="route_detail_map" class="h-[420px] w-full bg-slate-100 rounded-2xl border border-slate-200 relative z-10"></div>
            <div class="flex items-center justify-around text-xs text-slate-500 bg-slate-50 rounded-xl p-3 border border-slate-200/60">
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span>Hub xuất phát</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span>Điểm lấy hàng</span>
                <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></span>Điểm giao hàng</span>
                <span class="flex items-center"><span class="w-8 h-1 bg-blue-500 rounded mr-2"></span>Đường đi OSRM</span>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 flex justify-end flex-shrink-0">
            <button onclick="closeRouteMapModal()" class="px-4 py-2 text-xs font-bold rounded-xl text-slate-600 bg-slate-100 hover:bg-slate-200">Đóng</button>
        </div>
    </div>
</div>

<script>
    // Assign Shipper Modal
    function openAssignModal(routeId, currentShipper) {
        document.getElementById('assignRouteLabel').innerText = 'Tuyến #' + routeId + ' (hiện tại: ' + currentShipper + ')';
        document.getElementById('assignShipperForm').action = '/admin/routes/' + routeId + '/assign-shipper';
        document.getElementById('assignShipperModal').classList.remove('hidden');
    }

    // Route Map Modal (OSRM)
    let routeMap = null;
    let routeMapLayers = [];

    function openRouteMapModal(routeId, hub, routeOrders) {
        document.getElementById('modal_route_title').innerText = 'Bản Đồ Lộ Trình Tuyến #' + routeId;
        document.getElementById('routeMapModal').classList.remove('hidden');

        setTimeout(() => {
            if (!routeMap) {
                routeMap = L.map('route_detail_map').setView([21.028511, 105.804817], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19, attribution: '© OpenStreetMap'
                }).addTo(routeMap);
            }
            routeMapLayers.forEach(l => routeMap.removeLayer(l));
            routeMapLayers = [];

            let points = [], bounds = [];

            const redIcon = L.divIcon({
                className: '',
                html: `<div style="background:#ef4444;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                           <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                           <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #ef4444;"></div>
                       </div>`,
                iconSize: [32, 40], iconAnchor: [16, 40]
            });
            const blueIcon = L.divIcon({
                className: '',
                html: `<div style="background:#3b82f6;border:2px dashed rgba(255,255,255,0.9);border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                           <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                           <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #3b82f6;"></div>
                       </div>`,
                iconSize: [28, 36], iconAnchor: [14, 36]
            });
            const greenIcon = (seq) => L.divIcon({
                className: '',
                html: `<div style="background:#10b981;border:2px solid white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                           <span style="color:white;font-weight:900;font-size:11px;">${seq}</span>
                           <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #10b981;"></div>
                       </div>`,
                iconSize: [28, 36], iconAnchor: [14, 36]
            });

            if (hub && hub.latitude && hub.longitude) {
                let hlat = parseFloat(hub.latitude), hlng = parseFloat(hub.longitude);
                let m = L.marker([hlat, hlng], {icon: redIcon}).addTo(routeMap).bindPopup('<b>Hub: ' + hub.name + '</b><br>' + hub.address);
                routeMapLayers.push(m);
                points.push([hlat, hlng]); bounds.push([hlat, hlng]);
            }

            routeOrders.forEach(stop => {
                if (!stop.order) return;
                if (stop.order.pickup_lat && stop.order.pickup_lng) {
                    let plat = parseFloat(stop.order.pickup_lat), plng = parseFloat(stop.order.pickup_lng);
                    let m = L.marker([plat, plng], {icon: blueIcon}).addTo(routeMap).bindPopup('<b>Nhận #' + stop.stop_sequence + ': ' + stop.order.order_code + '</b><br>' + stop.order.pickup_address);
                    routeMapLayers.push(m); points.push([plat, plng]); bounds.push([plat, plng]);
                }
                if (stop.order.delivery_lat && stop.order.delivery_lng) {
                    let dlat = parseFloat(stop.order.delivery_lat), dlng = parseFloat(stop.order.delivery_lng);
                    let m = L.marker([dlat, dlng], {icon: greenIcon(stop.stop_sequence)}).addTo(routeMap).bindPopup('<b>Giao #' + stop.stop_sequence + ': ' + stop.order.order_code + '</b><br>' + stop.order.delivery_address);
                    routeMapLayers.push(m); points.push([dlat, dlng]); bounds.push([dlat, dlng]);
                }
            });

            if (points.length > 1) {
                let coordStr = points.map(p => p[1] + ',' + p[0]).join(';');
                fetch('https://router.project-osrm.org/route/v1/driving/' + coordStr + '?overview=full&geometries=geojson')
                    .then(r => r.json())
                    .then(data => {
                        if (data.code === 'Ok' && data.routes.length > 0) {
                            let line = L.geoJSON(data.routes[0].geometry, {style: {color:'#2563eb', weight:5, opacity:0.85}}).addTo(routeMap);
                            routeMapLayers.push(line);
                        } else {
                            let line = L.polyline(points, {color:'#ef4444', dashArray:'8,6', weight:3}).addTo(routeMap);
                            routeMapLayers.push(line);
                        }
                    }).catch(() => {
                        let line = L.polyline(points, {color:'#ef4444', dashArray:'8,6', weight:3}).addTo(routeMap);
                        routeMapLayers.push(line);
                    });
            }

            if (bounds.length > 0) routeMap.fitBounds(bounds, {padding:[40,40]});
            routeMap.invalidateSize();
        }, 200);
    }

    function closeRouteMapModal() {
        document.getElementById('routeMapModal').classList.add('hidden');
    }

    // Edit Route Modal
    function openEditRouteModal(routeId, distance, time) {
        document.getElementById('edit_route_id_label').innerText = 'Tuyến #' + routeId;
        document.getElementById('edit_route_distance').value = distance;
        document.getElementById('edit_route_time').value = time;
        document.getElementById('editRouteForm').action = '/admin/routes/' + routeId;
        document.getElementById('editRouteModal').classList.remove('hidden');
    }

    // Filter Routes client-side
    function filterRoutes(mode) {
        // Update tab styling
        document.querySelectorAll('.route-tab-btn').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            btn.classList.add('text-slate-500', 'hover:text-slate-700');
        });

        const activeBtn = document.getElementById(`tab-btn-${mode}`);
        if (activeBtn) {
            activeBtn.classList.remove('text-slate-500', 'hover:text-slate-700');
            activeBtn.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        }

        // Show/hide cards
        document.querySelectorAll('.route-card').forEach(card => {
            const isAssigned = card.getAttribute('data-assigned') === 'true';
            if (mode === 'all') {
                card.classList.remove('hidden');
            } else if (mode === 'unassigned') {
                if (!isAssigned) card.classList.remove('hidden');
                else card.classList.add('hidden');
            } else if (mode === 'assigned') {
                if (isAssigned) card.classList.remove('hidden');
                else card.classList.add('hidden');
            }
        });
    }
</script>


<div id="editRouteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Chỉnh sửa Tuyến đường</h3>
                <p class="text-[10px] text-slate-400 mt-0.5" id="edit_route_id_label"></p>
            </div>
            <button onclick="document.getElementById('editRouteModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="editRouteForm" method="POST" class="p-6 space-y-4">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Quãng đường (km) *</label>
                    <input type="number" id="edit_route_distance" name="total_distance" step="0.1" min="0" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Thời gian ước tính (phút) *</label>
                    <input type="number" id="edit_route_time" name="estimated_time" min="1" required
                        class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-xs bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('editRouteModal').classList.add('hidden')"
                    class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-xs font-bold rounded-xl text-white bg-blue-600 hover:bg-blue-700">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\danht\OneDrive\Desktop\toi_uu_giao_van\resources\views/admin/routes.blade.php ENDPATH**/ ?>