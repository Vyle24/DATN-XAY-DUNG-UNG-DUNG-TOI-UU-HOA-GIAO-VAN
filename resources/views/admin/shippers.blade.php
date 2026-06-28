@extends('layouts.admin')

@section('header_title', 'Quản Lý Shipper')

@section('content')

<!-- Statistics Cards Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng số Shipper</span>
        <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Trong danh bạ</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Shipper Hoạt Động</span>
        <span class="text-xl font-extrabold text-emerald-600 mt-1 block">{{ $stats['active'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Đang bật Online</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Shipper Tạm Dừng</span>
        <span class="text-xl font-extrabold text-slate-500 mt-1 block">{{ $stats['inactive'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Khóa/Tắt nhận đơn</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng ví ký quỹ</span>
        <span class="text-xl font-extrabold text-blue-600 mt-1 block">{{ number_format($stats['total_wallet']) }}đ</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Trung bình: {{ number_format($stats['avg_wallet']) }}đ</span>
    </div>
</div>

<!-- Header Controls -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h3 class="text-lg font-bold text-slate-800">Hồ Sơ Nhân Viên Giao Vận (Shippers)</h3>
        <p class="text-sm text-slate-500 font-medium">Đăng ký mới, phân quyền truy cập di động, giám sát hoạt động và điều chỉnh số dư ví</p>
    </div>
    <div>
        <button onclick="openCreateModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-sm font-semibold rounded-xl text-white shadow-md shadow-blue-600/10 transition duration-150">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Đăng ký Shipper mới
        </button>
    </div>
</div>

<!-- Search & Advanced Filters Form -->
<form action="{{ route('admin.shippers') }}" method="GET" class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Từ khóa tìm kiếm</label>
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên, email, biển số..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @if(request('search'))
                <a href="{{ route('admin.shippers') }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-650">✕</a>
            @endif
        </div>
    </div>
    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Trạng thái hoạt động</label>
        <select name="status" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Online (Đang chạy)</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tạm dừng (Offline)</option>
        </select>
    </div>
    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Loại phương tiện</label>
        <select name="vehicle_type" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
            <option value="all" {{ request('vehicle_type') === 'all' ? 'selected' : '' }}>Tất cả xe</option>
            <option value="Motorbike" {{ request('vehicle_type') === 'Motorbike' ? 'selected' : '' }}>Xe máy (Motorbike)</option>
            <option value="Truck" {{ request('vehicle_type') === 'Truck' ? 'selected' : '' }}>Xe tải (Truck)</option>
            <option value="Car" {{ request('vehicle_type') === 'Car' ? 'selected' : '' }}>Ô tô (Car)</option>
        </select>
    </div>
    <div class="flex items-end">
        <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-sm transition">
            Áp dụng bộ lọc
        </button>
    </div>
</form>

<!-- Shippers List Card -->
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-6 py-4">Tài Khoản / Họ Tên</th>
                    <th class="px-6 py-4">Liên Hệ</th>
                    <th class="px-6 py-4">Phương Tiện / BKS</th>
                    <th class="px-6 py-4">Số Dư Ví</th>
                    <th class="px-6 py-4 text-center">Đơn Đang Giao</th>
                    <th class="px-6 py-4 text-center">Hoạt Động</th>
                    <th class="px-6 py-4 text-right">Hành Động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @forelse($shippers as $shipper)
                @php $activeOrders = $shipper->active_orders_count ?? 0; @endphp
                <tr class="hover:bg-slate-50/20 transition">
                    <!-- Name -->
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="h-9 w-9 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 flex items-center justify-center font-extrabold text-sm mr-3">
                                {{ substr($shipper->user->username ?? 'S', 0, 1) }}
                            </span>
                            <div>
                                <div class="font-bold text-slate-900">{{ $shipper->user->username ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400">ID Shipper: #{{ $shipper->id }}</div>
                            </div>
                        </div>
                    </td>
                    <!-- Contact -->
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $shipper->user->email ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $shipper->user->phone ?? 'N/A' }}</div>
                    </td>
                    <!-- Vehicle -->
                    <td class="px-6 py-4">
                        <div class="font-semibold text-slate-800">{{ $shipper->vehicle_type }}</div>
                        <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $shipper->license_no }}</div>
                    </td>
                    <!-- Wallet -->
                    <td class="px-6 py-4 font-bold text-emerald-600">
                        {{ number_format($shipper->wallet_balance) }}đ
                    </td>
                    <!-- Active Orders -->
                    <td class="px-6 py-4 text-center">
                        @if($activeOrders > 0)
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-700 font-black text-sm border border-orange-200">{{ $activeOrders }}</span>
                            <div class="text-[10px] text-orange-500 mt-0.5">giao</div>
                        @else
                            <span class="text-[10px] text-emerald-500 font-bold">✔ Rảnh</span>
                        @endif
                    </td>
                    <!-- Status -->
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.shippers.toggle', $shipper->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold transition shadow-sm border {{ $shipper->is_active ? 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-100' : 'bg-slate-100 text-slate-500 border-slate-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $shipper->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $shipper->is_active ? 'Online' : 'Tạm Dừng' }}
                            </button>
                        </form>
                    </td>
                    <!-- Actions -->
                    <td class="px-6 py-4 text-right text-xs font-bold space-x-2">
                        <a href="{{ route('admin.dispatch') }}" class="text-indigo-600 hover:text-indigo-800 hover:underline">Phân Công</a>
                        <button onclick="openEditModal({{ json_encode($shipper) }}, {{ json_encode($shipper->user) }})" class="text-blue-600 hover:text-blue-800 hover:underline">Sửa</button>
                        
                        <form action="{{ route('admin.shippers.destroy', $shipper->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hồ sơ của shipper {{ $shipper->user->username ?? '' }}? Thao tác này cũng gỡ bỏ tài khoản người dùng!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 hover:underline">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">Không tìm thấy shipper phù hợp.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($shippers->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $shippers->links() }}
    </div>
    @endif
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Create Shipper Modal -->
<div id="createModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Đăng Ký Tài Khoản Shipper Mới</h3>
            <button onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.shippers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tên tài khoản *</label>
                    <input type="text" name="username" required placeholder="shipper_minh" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email liên lạc *</label>
                    <input type="email" name="email" required placeholder="minh.shipper@mail.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Số điện thoại *</label>
                    <input type="text" name="phone" required placeholder="090XXXXXXX" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mật khẩu ban đầu *</label>
                    <input type="password" name="password" required placeholder="Nhập mật khẩu" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Biển số xe (BKS) *</label>
                    <input type="text" name="license_no" required placeholder="29-K1 999.99" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Loại phương tiện *</label>
                    <select name="vehicle_type" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <option value="Motorbike">Xe máy (Motorbike)</option>
                        <option value="Truck">Xe tải (Truck)</option>
                        <option value="Car">Ô tô (Car)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Số dư ví ban đầu (đ) *</label>
                <input type="number" name="wallet_balance" required placeholder="0" value="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700">Đăng ký Shipper</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Shipper Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Cập Nhật Hồ Sơ Shipper</h3>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editShipperForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tên tài khoản *</label>
                    <input type="text" id="edit_username" name="username" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email liên lạc *</label>
                    <input type="email" id="edit_email" name="email" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Số điện thoại *</label>
                    <input type="text" id="edit_phone" name="phone" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mật khẩu (Để trống nếu giữ nguyên)</label>
                    <input type="password" name="password" placeholder="Đổi mật khẩu" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Biển số xe (BKS) *</label>
                    <input type="text" id="edit_license_no" name="license_no" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Loại phương tiện *</label>
                    <select id="edit_vehicle_type" name="vehicle_type" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <option value="Motorbike">Xe máy (Motorbike)</option>
                        <option value="Truck">Xe tải (Truck)</option>
                        <option value="Car">Ô tô (Car)</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Số dư ví (đ) *</label>
                <input type="number" id="edit_wallet_balance" name="wallet_balance" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 font-bold">Cập nhật hồ sơ</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function openEditModal(shipper, user) {
        document.getElementById('edit_username').value = user.username || '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_license_no').value = shipper.license_no || '';
        document.getElementById('edit_vehicle_type').value = shipper.vehicle_type || 'Motorbike';
        document.getElementById('edit_wallet_balance').value = Math.round(shipper.wallet_balance) || 0;

        const form = document.getElementById('editShipperForm');
        form.action = `/admin/shippers/${shipper.id}`;

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection
