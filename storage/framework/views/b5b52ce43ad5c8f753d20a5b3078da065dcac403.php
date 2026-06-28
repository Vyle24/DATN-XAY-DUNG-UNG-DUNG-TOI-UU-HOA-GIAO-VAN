<?php $__env->startSection('header_title', 'Quản Lý Đơn Hàng'); ?>

<?php $__env->startSection('content'); ?>

<!-- Statistics Cards Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng đơn hàng</span>
        <span class="text-xl font-extrabold text-slate-800 mt-1 block"><?php echo e($stats['total']); ?></span>
        <span class="text-[10px] text-slate-400 mt-1 block">Toàn bộ hệ thống</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đơn đang giao</span>
        <span class="text-xl font-extrabold text-amber-600 mt-1 block"><?php echo e($stats['processing']); ?></span>
        <span class="text-[10px] text-slate-400 mt-1 block">Shipper đang vận chuyển</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Giao thành công</span>
        <span class="text-xl font-extrabold text-emerald-600 mt-1 block"><?php echo e($stats['delivered']); ?></span>
        <span class="text-[10px] text-slate-400 mt-1 block">Đã hoàn tất thanh toán</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng cước phí thu</span>
        <span class="text-xl font-extrabold text-blue-600 mt-1 block"><?php echo e(number_format($stats['total_fee'])); ?>đ</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Khối lượng: <?php echo e(number_format($stats['total_weight'], 1)); ?>kg</span>
    </div>
</div>

<!-- Header Controls -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h3 class="text-base font-bold text-slate-800">Danh Sách Toàn Bộ Đơn Hàng</h3>
        <p class="text-xs text-slate-400 font-medium">Tạo mới, cập nhật thông tin, điều phối shipper và theo dõi lịch trình hành trình</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="<?php echo e(route('admin.orders.export', request()->query())); ?>"
            class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 text-emerald-700 text-xs font-bold rounded-xl shadow-sm transition duration-150">
            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Xuất CSV
        </a>
        <button onclick="openCreateModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-xs font-semibold rounded-xl text-white shadow-md shadow-blue-600/10 transition duration-150">
            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Tạo đơn hàng mới
        </button>
    </div>
</div>

<!-- Search & Advanced Filters Form -->
<form action="<?php echo e(route('admin.orders')); ?>" method="GET" class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-5 mb-6 space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Search type + query -->
        <div class="md:col-span-2 flex gap-2">
            <div class="w-1/3">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Mục tìm kiếm</label>
                <select name="search_type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none">
                    <option value="all" <?php echo e(request('search_type') === 'all' ? 'selected' : ''); ?>>Tất cả trường</option>
                    <option value="code" <?php echo e(request('search_type') === 'code' ? 'selected' : ''); ?>>Mã đơn hàng</option>
                    <option value="address" <?php echo e(request('search_type') === 'address' ? 'selected' : ''); ?>>Địa chỉ giao nhận</option>
                    <option value="customer" <?php echo e(request('search_type') === 'customer' ? 'selected' : ''); ?>>Tên / SĐT Khách</option>
                </select>
            </div>
            <div class="w-2/3">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Từ khóa</label>
                <div class="relative">
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Mã đơn, địa chỉ, khách..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <?php if(request('search')): ?>
                        <a href="<?php echo e(route('admin.orders', request()->except('search'))); ?>" class="absolute right-3 top-3 text-slate-400 hover:text-slate-650">✕</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Trạng thái giao vận</label>
            <select name="status" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none">
                <option value="all" <?php echo e(request('status') === 'all' ? 'selected' : ''); ?>>Tất cả trạng thái</option>
                <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Chờ xử lý (Pending)</option>
                <option value="processing" <?php echo e(request('status') === 'processing' ? 'selected' : ''); ?>>Đang giao (Processing)</option>
                <option value="delivered" <?php echo e(request('status') === 'delivered' ? 'selected' : ''); ?>>Đã giao (Delivered)</option>
                <option value="failed" <?php echo e(request('status') === 'failed' ? 'selected' : ''); ?>>Thất bại (Failed)</option>
            </select>
        </div>

        <!-- Hub Filter -->
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Trạm điều phối (Hub)</label>
            <select name="hub_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none">
                <option value="">Tất cả Trạm Hub</option>
                <?php $__currentLoopData = $hubs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($h->id); ?>" <?php echo e(request('hub_id') == $h->id ? 'selected' : ''); ?>><?php echo e($h->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>

    <!-- Advanced sliders -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 border-t border-slate-100 pt-3">
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Shipper nhận đơn</label>
            <select name="shipper_id" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none">
                <option value="">Tất cả Shipper</option>
                <?php $__currentLoopData = $shippers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($s->id); ?>" <?php echo e(request('shipper_id') == $s->id ? 'selected' : ''); ?>><?php echo e($s->user->username ?? ''); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cước tối thiểu (đ)</label>
            <input type="number" name="min_fee" value="<?php echo e(request('min_fee')); ?>" placeholder="Ví dụ: 10000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none">
        </div>

        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cước tối đa (đ)</label>
            <input type="number" name="max_fee" value="<?php echo e(request('max_fee')); ?>" placeholder="Ví dụ: 100000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs focus:bg-white focus:outline-none">
        </div>

        <div class="flex items-end">
            <button type="submit" class="w-full py-2.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                Áp dụng bộ lọc
            </button>
        </div>
    </div>
</form>

<!-- Orders Table Card -->
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-6 py-4">Mã Đơn</th>
                    <th class="px-6 py-4">Khách Hàng</th>
                    <th class="px-6 py-4">Hành Trình Giao Vận</th>
                    <th class="px-6 py-4">Thông Số & Phí</th>
                    <th class="px-6 py-4">Shipper</th>
                    <th class="px-6 py-4 text-center">Trạng Thái</th>
                    <th class="px-6 py-4 text-right">Hành Động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-slate-50/20 transition">
                    <!-- Code -->
                    <td class="px-6 py-4 font-bold text-blue-600">
                        <button type="button" onclick="openTrackingModal(<?php echo e(json_encode($order)); ?>)" class="hover:underline focus:outline-none text-left">
                            <?php echo e($order->order_code); ?>

                        </button>
                        <span class="block text-[10px] text-slate-400 font-mono">Hub: <?php echo e($order->hub->name ?? 'N/A'); ?></span>
                    </td>
                    <!-- Customer -->
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-900"><?php echo e($order->customer->user->username ?? 'Khách lẻ'); ?></div>
                        <div class="text-[11px] text-slate-400"><?php echo e($order->customer->user->phone ?? ''); ?></div>
                    </td>
                    <!-- Path -->
                    <td class="px-6 py-4">
                        <div class="space-y-1 text-xs">
                            <div class="flex items-center text-slate-650">
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-2"></span>
                                <span class="truncate max-w-[200px]" title="<?php echo e($order->pickup_address); ?>"><?php echo e($order->pickup_address); ?></span>
                            </div>
                            <div class="flex items-center text-slate-650">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2"></span>
                                <span class="truncate max-w-[200px]" title="<?php echo e($order->delivery_address); ?>"><?php echo e($order->delivery_address); ?></span>
                            </div>
                        </div>
                    </td>
                    <!-- weight / fee -->
                    <td class="px-6 py-4 text-xs">
                        <div><span class="text-slate-400">Nặng:</span> <span class="font-semibold text-slate-800"><?php echo e($order->total_weight); ?> kg</span></div>
                        <div class="mt-0.5"><span class="text-slate-400">Phí:</span> <span class="font-semibold text-slate-900"><?php echo e(number_format($order->shipping_fee)); ?>đ</span></div>
                    </td>
                    <!-- Shipper -->
                    <td class="px-6 py-4">
                        <?php if($order->shipper): ?>
                            <div class="font-semibold text-slate-900"><?php echo e($order->shipper->user->username ?? ''); ?></div>
                            <div class="text-[10px] text-slate-400"><?php echo e($order->shipper->license_no ?? ''); ?></div>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500">Chưa gán</span>
                        <?php endif; ?>
                    </td>
                    <!-- Status -->
                    <td class="px-6 py-4 text-center">
                        <?php if($order->status === 'delivered'): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Đã giao</span>
                        <?php elseif($order->status === 'processing'): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100">Đang giao</span>
                        <?php elseif($order->status === 'failed'): ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100">Thất bại</span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100">Chờ xử lý</span>
                        <?php endif; ?>
                    </td>
                    <!-- Actions -->
                    <td class="px-6 py-4 text-right text-xs font-bold space-x-2.5 flex justify-end items-center">
                        <a href="<?php echo e(route('admin.orders.print', $order->id)); ?>" target="_blank" class="text-slate-500 hover:text-slate-800 hover:underline mr-1" title="In Vận Đơn">In</a>
                        <button onclick="openTrackingModal(<?php echo e(json_encode($order)); ?>)" class="text-emerald-600 hover:text-emerald-800 hover:underline">Hành trình</button>
                        <button onclick="openEditModal(<?php echo e(json_encode($order)); ?>)" class="text-blue-600 hover:text-blue-800 hover:underline">Sửa</button>
                        <form action="<?php echo e(route('admin.orders.destroy', $order->id)); ?>" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa đơn hàng <?php echo e($order->order_code); ?> không?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="text-rose-600 hover:text-rose-800 hover:underline">Xóa</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">Không tìm thấy đơn hàng phù hợp.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Laravel standard pagination container -->
    <?php if($orders->hasPages()): ?>
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        <?php echo e($orders->links()); ?>

    </div>
    <?php endif; ?>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Create Order Modal -->
<div id="createModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Tạo Đơn Hàng Mới</h3>
            <button type="button" onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="<?php echo e(route('admin.orders.store')); ?>" method="POST" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mã đơn hàng *</label>
                    <input type="text" name="order_code" required placeholder="ORD-XXXX" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Khách hàng *</label>
                    <select name="customer_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>"><?php echo e($c->user->username ?? ''); ?> (<?php echo e($c->membership_level ?? 'Normal'); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Điểm điều phối (Hub) *</label>
                    <select name="hub_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <?php $__currentLoopData = $hubs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phương thức thanh toán</label>
                    <input type="text" name="payment_method" placeholder="COD, Bank, v.v." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-3">
                <span class="text-xs font-bold text-slate-400 block mb-2">Hành trình chi tiết (Nhấp bản đồ bên dưới để lấy toạ độ)</span>
                
                <!-- Interactive Selector Map -->
                <div class="flex items-center space-x-2 mb-2 text-xs">
                    <span class="font-bold text-slate-500">Chế độ chọn:</span>
                    <button type="button" id="btn_set_pickup" onclick="setMapSelectionMode('pickup')" class="px-3 py-1 bg-blue-600 text-white rounded-lg font-semibold shadow-sm">Đặt Điểm Lấy</button>
                    <button type="button" id="btn_set_delivery" onclick="setMapSelectionMode('delivery')" class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg font-semibold">Đặt Điểm Giao</button>
                </div>
                
                <div id="create_map" class="h-44 bg-slate-100 rounded-xl mb-3 border border-slate-200/60 relative z-10"></div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Địa chỉ lấy hàng *</label>
                        <input type="text" name="pickup_address" required placeholder="Số 1 Xuân Thủy, Cầu Giấy" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Vĩ độ (Lat) *</label>
                            <input type="number" step="any" name="pickup_lat" required placeholder="21.0362" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:bg-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Kinh độ (Lng) *</label>
                            <input type="number" step="any" name="pickup_lng" required placeholder="105.7905" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:bg-white focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Địa chỉ giao hàng *</label>
                        <input type="text" name="delivery_address" required placeholder="Ngõ 165 Cầu Giấy" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Vĩ độ (Lat) *</label>
                            <input type="number" step="any" name="delivery_lat" required placeholder="21.0345" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:bg-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Kinh độ (Lng) *</label>
                            <input type="number" step="any" name="delivery_lng" required placeholder="105.7934" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:bg-white focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Trọng lượng (kg) *</label>
                    <input type="number" step="any" name="total_weight" required placeholder="0.5" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phí giao hàng (đ) *</label>
                    <input type="number" name="shipping_fee" required placeholder="15000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phân công Shipper</label>
                    <select name="shipper_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <option value="">-- Chưa gán (Để trống) --</option>
                        <?php $__currentLoopData = $shippers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>"><?php echo e($s->user->username ?? ''); ?> (<?php echo e($s->license_no); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Trạng thái đơn *</label>
                    <select name="status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <option value="pending">Chờ xử lý (Pending)</option>
                        <option value="processing">Đang đi giao (Processing)</option>
                        <option value="delivered">Đã giao thành công (Delivered)</option>
                        <option value="failed">Thất bại (Failed)</option>
                    </select>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700">Tạo đơn hàng</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Order Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Cập Nhật Thông Tin Đơn Hàng</h3>
            <button type="button" onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editOrderForm" method="POST" class="p-6 space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mã đơn hàng *</label>
                    <input type="text" id="edit_order_code" name="order_code" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Khách hàng *</label>
                    <select id="edit_customer_id" name="customer_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->id); ?>"><?php echo e($c->user->username ?? ''); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Điểm điều phối (Hub) *</label>
                    <select id="edit_hub_id" name="hub_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <?php $__currentLoopData = $hubs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($h->id); ?>"><?php echo e($h->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phương thức thanh toán</label>
                    <input type="text" id="edit_payment_method" name="payment_method" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-3">
                <span class="text-xs font-bold text-slate-400 block mb-2">Hành trình chi tiết (Nhấp bản đồ để đặt lại toạ độ)</span>
                
                <div class="flex items-center space-x-2 mb-2 text-xs">
                    <span class="font-bold text-slate-500">Chế độ chọn:</span>
                    <button type="button" id="edit_btn_set_pickup" onclick="setEditMapSelectionMode('pickup')" class="px-3 py-1 bg-blue-600 text-white rounded-lg font-semibold shadow-sm">Đặt Điểm Lấy</button>
                    <button type="button" id="edit_btn_set_delivery" onclick="setEditMapSelectionMode('delivery')" class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg font-semibold">Đặt Điểm Giao</button>
                </div>
                
                <div id="edit_map" class="h-44 bg-slate-100 rounded-xl mb-3 border border-slate-200/60 relative z-10"></div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Địa chỉ lấy hàng *</label>
                        <input type="text" id="edit_pickup_address" name="pickup_address" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Vĩ độ *</label>
                            <input type="number" step="any" id="edit_pickup_lat" name="pickup_lat" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:bg-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Kinh độ *</label>
                            <input type="number" step="any" id="edit_pickup_lng" name="pickup_lng" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:bg-white focus:outline-none">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mt-3">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Địa chỉ giao hàng *</label>
                        <input type="text" id="edit_delivery_address" name="delivery_address" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Vĩ độ *</label>
                            <input type="number" step="any" id="edit_delivery_lat" name="delivery_lat" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:bg-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Kinh độ *</label>
                            <input type="number" step="any" id="edit_delivery_lng" name="delivery_lng" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-2 py-2 text-xs focus:bg-white focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Trọng lượng (kg) *</label>
                    <input type="number" step="any" id="edit_total_weight" name="total_weight" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phí giao hàng (đ) *</label>
                    <input type="number" id="edit_shipping_fee" name="shipping_fee" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Phân công Shipper</label>
                    <select id="edit_shipper_id" name="shipper_id" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <option value="">-- Chưa gán (Để trống) --</option>
                        <?php $__currentLoopData = $shippers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($s->id); ?>"><?php echo e($s->user->username ?? ''); ?> (<?php echo e($s->license_no); ?>)</option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Trạng thái đơn *</label>
                    <select id="edit_status" name="status" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <option value="pending">Chờ xử lý (Pending)</option>
                        <option value="processing">Đang đi giao (Processing)</option>
                        <option value="delivered">Đã giao thành công (Delivered)</option>
                        <option value="failed">Thất bại (Failed)</option>
                    </select>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 font-bold">Cập nhật đơn hàng</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Tracking Logs & Assignments Detail Modal -->
<div id="trackingModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[85vh] overflow-hidden shadow-2xl border border-slate-100 flex flex-col">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between flex-shrink-0">
            <div>
                <h3 class="text-base font-bold text-slate-800">Chi Tiết Hành Trình Đơn Hàng</h3>
                <p class="text-xs text-slate-400 mt-0.5">Mã đơn: <span id="tracking_order_code" class="font-bold text-blue-600"></span></p>
            </div>
            <button type="button" onclick="closeModal('trackingModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto space-y-6 flex-grow">
            <!-- 3.0 Map route visualization -->
            <div>
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2.5">Bản đồ hành trình thực tế (Pickup A -> Delivery B)</span>
                <div id="tracking_map" class="h-52 bg-slate-100 rounded-xl border border-slate-200 relative z-10"></div>
            </div>

            <!-- 3.1. Tracking Logs Timeline -->
            <div>
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Lịch sử cập nhật trạng thái (Tracking Logs)</span>
                <div id="tracking_timeline_container" class="relative pl-6 space-y-4">
                    <!-- Vertical Line -->
                    <div class="absolute left-[9px] top-1.5 bottom-1.5 w-0.5 bg-slate-200"></div>
                    <!-- Dynamic timeline logs injected here -->
                </div>
            </div>

            <!-- 3.2. Shipper Assignments Log -->
            <div class="border-t border-slate-100 pt-5">
                <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4">Nhật ký điều phối Shipper (Assignments)</span>
                <div class="bg-slate-50 rounded-xl border border-slate-200/50 overflow-hidden">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-100 border-b border-slate-200 text-[10px] font-bold uppercase text-slate-500">
                                <th class="px-4 py-2.5">Shipper</th>
                                <th class="px-4 py-2.5">Ngày Điều Phối</th>
                                <th class="px-4 py-2.5 text-center">Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody id="assignments_table_body" class="divide-y divide-slate-100 text-slate-650">
                            <!-- Dynamic rows injected here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex justify-end flex-shrink-0">
            <button type="button" onclick="closeModal('trackingModal')" class="px-4 py-2 text-xs font-semibold rounded-xl text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 shadow-sm">Đóng</button>
        </div>
    </div>
</div>

<script>
    // Maps variables
    let createMap = null;
    let createPickupMarker = null;
    let createDeliveryMarker = null;
    let createMapMode = 'pickup';

    let editMap = null;
    let editPickupMarker = null;
    let editDeliveryMarker = null;
    let editMapMode = 'pickup';

    let trackingMap = null;
    let trackingPickupMarker = null;
    let trackingDeliveryMarker = null;
    let trackingRouteLine = null;

    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        
        // Initialize create map with delay for layout rendering
        setTimeout(() => {
            if (!createMap) {
                createMap = L.map('create_map').setView([21.028511, 105.804817], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(createMap);

                createMap.on('click', function(e) {
                    let lat = e.latlng.lat;
                    let lng = e.latlng.lng;
                    
                    if (createMapMode === 'pickup') {
                        document.getElementsByName('pickup_lat')[0].value = lat.toFixed(6);
                        document.getElementsByName('pickup_lng')[0].value = lng.toFixed(6);
                        
                        if (createPickupMarker) createMap.removeLayer(createPickupMarker);
                        createPickupMarker = L.marker([lat, lng], {
                            icon: L.divIcon({
                                className: '',
                                html: `<div style="background:#3b82f6;border:2px dashed rgba(255,255,255,0.9);border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                                           <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                           <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #3b82f6;"></div>
                                       </div>`,
                                iconSize: [28, 36],
                                iconAnchor: [14, 36]
                            })
                        }).addTo(createMap).bindPopup("Điểm lấy").openPopup();
                    } else {
                        document.getElementsByName('delivery_lat')[0].value = lat.toFixed(6);
                        document.getElementsByName('delivery_lng')[0].value = lng.toFixed(6);
                        
                        if (createDeliveryMarker) createMap.removeLayer(createDeliveryMarker);
                        createDeliveryMarker = L.marker([lat, lng], {
                            icon: L.divIcon({
                                className: '',
                                html: `<div style="background:#10b981;border:2px solid white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                                           <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                           <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #10b981;"></div>
                                       </div>`,
                                iconSize: [28, 36],
                                iconAnchor: [14, 36]
                            })
                        }).addTo(createMap).bindPopup("Điểm giao").openPopup();
                    }
                });
            }
            createMap.invalidateSize();
        }, 200);
    }

    function setMapSelectionMode(mode) {
        createMapMode = mode;
        if (mode === 'pickup') {
            document.getElementById('btn_set_pickup').className = "px-3 py-1 bg-blue-600 text-white rounded-lg font-semibold shadow-sm";
            document.getElementById('btn_set_delivery').className = "px-3 py-1 bg-slate-100 text-slate-650 rounded-lg font-semibold";
        } else {
            document.getElementById('btn_set_pickup').className = "px-3 py-1 bg-slate-100 text-slate-650 rounded-lg font-semibold";
            document.getElementById('btn_set_delivery').className = "px-3 py-1 bg-emerald-600 text-white rounded-lg font-semibold shadow-sm";
        }
    }

    function openEditModal(order) {
        document.getElementById('edit_order_code').value = order.order_code;
        document.getElementById('edit_customer_id').value = order.customer_id;
        document.getElementById('edit_hub_id').value = order.hub_id;
        document.getElementById('edit_payment_method').value = order.payment_method || '';
        document.getElementById('edit_pickup_address').value = order.pickup_address;
        document.getElementById('edit_pickup_lat').value = order.pickup_lat;
        document.getElementById('edit_pickup_lng').value = order.pickup_lng;
        document.getElementById('edit_delivery_address').value = order.delivery_address;
        document.getElementById('edit_delivery_lat').value = order.delivery_lat;
        document.getElementById('edit_delivery_lng').value = order.delivery_lng;
        document.getElementById('edit_total_weight').value = order.total_weight;
        document.getElementById('edit_shipping_fee').value = Math.round(order.shipping_fee);
        document.getElementById('edit_shipper_id').value = order.shipper_id || '';
        document.getElementById('edit_status').value = order.status;

        const form = document.getElementById('editOrderForm');
        form.action = `/admin/orders/${order.id}`;

        document.getElementById('editModal').classList.remove('hidden');

        // Initialize edit map
        setTimeout(() => {
            let plat = parseFloat(order.pickup_lat) || 21.028511;
            let plng = parseFloat(order.pickup_lng) || 105.804817;
            let dlat = parseFloat(order.delivery_lat) || 21.028511;
            let dlng = parseFloat(order.delivery_lng) || 105.804817;

            if (!editMap) {
                editMap = L.map('edit_map').setView([plat, plng], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(editMap);

                editMap.on('click', function(e) {
                    let lat = e.latlng.lat;
                    let lng = e.latlng.lng;
                    
                    if (editMapMode === 'pickup') {
                        document.getElementById('edit_pickup_lat').value = lat.toFixed(6);
                        document.getElementById('edit_pickup_lng').value = lng.toFixed(6);
                        
                        if (editPickupMarker) editMap.removeLayer(editPickupMarker);
                        editPickupMarker = L.marker([lat, lng], {
                            icon: L.divIcon({
                                className: '',
                                html: `<div style="background:#3b82f6;border:2px dashed rgba(255,255,255,0.9);border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                                           <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                           <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #3b82f6;"></div>
                                       </div>`,
                                iconSize: [28, 36],
                                iconAnchor: [14, 36]
                            })
                        }).addTo(editMap).bindPopup("Điểm lấy").openPopup();
                    } else {
                        document.getElementById('edit_delivery_lat').value = lat.toFixed(6);
                        document.getElementById('edit_delivery_lng').value = lng.toFixed(6);
                        
                        if (editDeliveryMarker) editMap.removeLayer(editDeliveryMarker);
                        editDeliveryMarker = L.marker([lat, lng], {
                            icon: L.divIcon({
                                className: '',
                                html: `<div style="background:#10b981;border:2px solid white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                                           <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                           <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #10b981;"></div>
                                       </div>`,
                                iconSize: [28, 36],
                                iconAnchor: [14, 36]
                            })
                        }).addTo(editMap).bindPopup("Điểm giao").openPopup();
                    }
                });
            } else {
                editMap.setView([plat, plng], 12);
            }

            // Remove old layers
            if (editPickupMarker) editMap.removeLayer(editPickupMarker);
            if (editDeliveryMarker) editMap.removeLayer(editDeliveryMarker);

            // Set markers
            editPickupMarker = L.marker([plat, plng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="background:#3b82f6;border:2px dashed rgba(255,255,255,0.9);border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                               <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                               <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #3b82f6;"></div>
                           </div>`,
                    iconSize: [28, 36],
                    iconAnchor: [14, 36]
                })
            }).addTo(editMap).bindPopup("Điểm lấy");

            editDeliveryMarker = L.marker([dlat, dlng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="background:#10b981;border:2px solid white;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 5px rgba(0,0,0,0.3);position:relative;">
                               <svg style="width:14px;height:14px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                               <div style="position:absolute;bottom:-5px;left:50%;transform:translateX(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:6px solid #10b981;"></div>
                           </div>`,
                    iconSize: [28, 36],
                    iconAnchor: [14, 36]
                })
            }).addTo(editMap).bindPopup("Điểm giao");

            editMap.invalidateSize();
        }, 200);
    }

    function setEditMapSelectionMode(mode) {
        editMapMode = mode;
        if (mode === 'pickup') {
            document.getElementById('edit_btn_set_pickup').className = "px-3 py-1 bg-blue-600 text-white rounded-lg font-semibold shadow-sm";
            document.getElementById('edit_btn_set_delivery').className = "px-3 py-1 bg-slate-100 text-slate-650 rounded-lg font-semibold";
        } else {
            document.getElementById('edit_btn_set_pickup').className = "px-3 py-1 bg-slate-100 text-slate-650 rounded-lg font-semibold";
            document.getElementById('edit_btn_set_delivery').className = "px-3 py-1 bg-emerald-600 text-white rounded-lg font-semibold shadow-sm";
        }
    }

    function openTrackingModal(order) {
        document.getElementById('tracking_order_code').innerText = order.order_code;

        // Populate Timeline Logs
        const timelineContainer = document.getElementById('tracking_timeline_container');
        timelineContainer.innerHTML = '';
        
        if (order.tracking_logs && order.tracking_logs.length > 0) {
            order.tracking_logs.forEach(log => {
                const item = document.createElement('div');
                item.className = 'relative flex items-start text-xs';
                const dateStr = log.created_at ? new Date(log.created_at).toLocaleString('vi-VN') : 'N/A';
                
                let badgeClass = 'border-slate-400 bg-slate-100 text-slate-700';
                if (log.status_at_time === 'delivered') badgeClass = 'border-emerald-500 bg-emerald-50 text-emerald-800';
                else if (log.status_at_time === 'processing') badgeClass = 'border-amber-500 bg-amber-50 text-amber-800';
                else if (log.status_at_time === 'created') badgeClass = 'border-blue-500 bg-blue-50 text-blue-800';
                else if (log.status_at_time === 'failed') badgeClass = 'border-rose-50 bg-rose-50 text-rose-800';

                item.innerHTML = `
                    <span class="absolute left-[-22px] top-1 h-3.5 w-3.5 rounded-full bg-white border-2 border-blue-600 ring-4 ring-blue-50"></span>
                    <div class="flex-grow">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-850">${log.location_name || 'Không xác định'}</span>
                            <span class="text-[10px] text-slate-400 font-medium">${dateStr}</span>
                        </div>
                        <p class="text-slate-500 mt-0.5">Trạng thái: <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider ${badgeClass}">${log.status_at_time}</span></p>
                        <p class="text-[9px] font-mono text-slate-400 mt-0.5">Tọa độ: ${log.lat ?? '0'}, ${log.lng ?? '0'}</p>
                    </div>
                `;
                timelineContainer.appendChild(item);
            });
        } else {
            timelineContainer.innerHTML = '<p class="text-xs text-slate-405 italic pl-2">Chưa ghi nhận hành trình (Mới tạo).</p>';
        }

        // Populate Shipper Assignments
        const tableBody = document.getElementById('assignments_table_body');
        tableBody.innerHTML = '';

        if (order.shipper_assignments && order.shipper_assignments.length > 0) {
            order.shipper_assignments.forEach(assign => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-slate-50/50';
                const dateStr = assign.assigned_at ? new Date(assign.assigned_at).toLocaleString('vi-VN') : 'N/A';
                
                let statusLabel = 'Chờ phản hồi';
                let labelClass = 'bg-slate-100 text-slate-650';
                if (assign.status === 'accepted') {
                    statusLabel = 'Đồng ý';
                    labelClass = 'bg-emerald-50 text-emerald-700 font-bold';
                } else if (assign.status === 'rejected') {
                    statusLabel = 'Từ chối';
                    labelClass = 'bg-rose-50 text-rose-700 font-bold';
                }

                row.innerHTML = `
                    <td class="px-4 py-3 font-semibold text-slate-800">${assign.shipper?.user?.username ?? 'Shipper đã xóa'}</td>
                    <td class="px-4 py-3 text-slate-500">${dateStr}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium ${labelClass}">${statusLabel}</span>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = '<tr><td colspan="3" class="px-4 py-4 text-center text-slate-400 italic">Chưa từng điều phối shipper cho đơn này.</td></tr>';
        }

        document.getElementById('trackingModal').classList.remove('hidden');

        // Draw Tracking Route Map
        setTimeout(() => {
            let plat = parseFloat(order.pickup_lat);
            let plng = parseFloat(order.pickup_lng);
            let dlat = parseFloat(order.delivery_lat);
            let dlng = parseFloat(order.delivery_lng);

            if (!trackingMap) {
                trackingMap = L.map('tracking_map').setView([plat, plng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap'
                }).addTo(trackingMap);
            } else {
                trackingMap.setView([plat, plng], 13);
            }

            if (trackingPickupMarker) trackingMap.removeLayer(trackingPickupMarker);
            if (trackingDeliveryMarker) trackingMap.removeLayer(trackingDeliveryMarker);
            if (trackingRouteLine) trackingMap.removeLayer(trackingRouteLine);

            trackingPickupMarker = L.marker([plat, plng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="background:#3b82f6;border:2px dashed rgba(255,255,255,0.9);border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                               <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                               <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #3b82f6;"></div>
                           </div>`,
                    iconSize: [32, 40],
                    iconAnchor: [16, 40]
                })
            }).addTo(trackingMap).bindPopup("<b>A: Điểm Lấy Hàng</b><br>" + order.pickup_address).openPopup();

            trackingDeliveryMarker = L.marker([dlat, dlng], {
                icon: L.divIcon({
                    className: '',
                    html: `<div style="background:#10b981;border:2px solid white;border-radius:50%;width:32px;height:32px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 6px rgba(0,0,0,0.3);position:relative;">
                               <svg style="width:16px;height:16px;color:white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                               <div style="position:absolute;bottom:-6px;left:50%;transform:translateX(-50%);border-left:5px solid transparent;border-right:5px solid transparent;border-top:7px solid #10b981;"></div>
                           </div>`,
                    iconSize: [32, 40],
                    iconAnchor: [16, 40]
                })
            }).addTo(trackingMap).bindPopup("<b>B: Điểm Giao Hàng</b><br>" + order.delivery_address);

            trackingRouteLine = L.polyline([[plat, plng], [dlat, dlng]], {
                color: '#3b82f6',
                weight: 3.5,
                dashArray: '6, 6'
            }).addTo(trackingMap);

            let group = new L.featureGroup([trackingPickupMarker, trackingDeliveryMarker]);
            trackingMap.fitBounds(group.getBounds().pad(0.25));
            trackingMap.invalidateSize();
        }, 200);
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\danht\OneDrive\Desktop\toi_uu_giao_van\resources\views/admin/orders.blade.php ENDPATH**/ ?>