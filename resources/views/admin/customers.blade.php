@extends('layouts.admin')

@section('header_title', 'Quản Lý Khách Hàng')

@section('content')

<!-- Statistics Cards Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng số khách hàng</span>
        <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Tài khoản thành viên</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Thành viên VIP (⭐)</span>
        <span class="text-xl font-extrabold text-amber-600 mt-1 block">{{ $stats['vip'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Khách hàng đặc biệt</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Gold/Silver (🟡/⚪)</span>
        <span class="text-xl font-extrabold text-slate-700 mt-1 block">{{ $stats['gold'] + $stats['silver'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Hội viên hạng trung</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng điểm tích lũy</span>
        <span class="text-xl font-extrabold text-blue-600 mt-1 block">{{ number_format($stats['total_points']) }} Pts</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Điểm thưởng lưu trữ</span>
    </div>
</div>

<!-- Header Controls -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h3 class="text-lg font-bold text-slate-800">Quản Lý Thành Viên & Khách Hàng</h3>
        <p class="text-sm text-slate-500 font-medium">Đăng ký tài khoản khách hàng, quản lý cấp độ hội viên và điểm thưởng tích lũy</p>
    </div>
    <div>
        <button onclick="openCreateModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-sm font-semibold rounded-xl text-white shadow-md shadow-blue-600/10 transition duration-150">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Đăng ký khách hàng mới
        </button>
    </div>
</div>

<!-- Search & Filters Form -->
<form action="{{ route('admin.customers') }}" method="GET" class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Từ khóa tìm kiếm</label>
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên, email, điện thoại..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @if(request('search'))
                <a href="{{ route('admin.customers', request()->except('search')) }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-650">✕</a>
            @endif
        </div>
    </div>

    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cấp độ hội viên</label>
        <select name="membership_level" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
            <option value="all" {{ request('membership_level') === 'all' ? 'selected' : '' }}>Tất cả cấp độ</option>
            <option value="Standard" {{ request('membership_level') === 'Standard' ? 'selected' : '' }}>Standard (Thường)</option>
            <option value="Silver" {{ request('membership_level') === 'Silver' ? 'selected' : '' }}>Silver (Bạc)</option>
            <option value="Gold" {{ request('membership_level') === 'Gold' ? 'selected' : '' }}>Gold (Vàng)</option>
            <option value="VIP" {{ request('membership_level') === 'VIP' ? 'selected' : '' }}>VIP</option>
        </select>
    </div>

    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Điểm tối thiểu (Pts)</label>
        <input type="number" name="min_points" value="{{ request('min_points') }}" placeholder="Ví dụ: 100" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
    </div>

    <div class="flex items-end">
        <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-sm transition">
            Áp dụng bộ lọc
        </button>
    </div>
</form>

<!-- Customers List Table -->
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-6 py-4">Họ Tên / Tài Khoản</th>
                    <th class="px-6 py-4">Liên Hệ</th>
                    <th class="px-6 py-4">Cấp Độ Hội Viên</th>
                    <th class="px-6 py-4">Điểm Tích Lũy</th>
                    <th class="px-6 py-4 text-right">Hành Động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @forelse($customers as $customer)
                <tr class="hover:bg-slate-50/20 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <span class="h-9 w-9 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 flex items-center justify-center font-extrabold text-sm mr-3">
                                {{ substr($customer->user->username ?? 'C', 0, 1) }}
                            </span>
                            <div>
                                <div class="font-bold text-slate-900">{{ $customer->user->username ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400">Customer ID: #{{ $customer->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800">{{ $customer->user->email ?? 'N/A' }}</div>
                        <div class="text-xs text-slate-400 mt-0.5">{{ $customer->user->phone ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @if($customer->membership_level === 'VIP')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 shadow-sm">
                                ⭐ VIP Member
                            </span>
                        @elseif($customer->membership_level === 'Gold')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-800 border border-yellow-200">
                                🟡 Gold Member
                            </span>
                        @elseif($customer->membership_level === 'Silver')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                ⚪ Silver Member
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-slate-50 text-slate-650 border border-slate-150">
                                Standard
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-800">
                        {{ number_format($customer->points ?? 0) }} Pts
                    </td>
                    <td class="px-6 py-4 text-right text-xs font-bold space-x-2">
                        <button onclick="openEditModal({{ json_encode($customer) }}, {{ json_encode($customer->user) }})" class="text-blue-600 hover:text-blue-800 hover:underline">Sửa</button>
                        
                        <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hồ sơ khách hàng {{ $customer->user->username ?? '' }}? Hệ thống sẽ gỡ bỏ vĩnh viễn tài khoản!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 hover:underline">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">Không tìm thấy khách hàng phù hợp.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $customers->links() }}
    </div>
    @endif
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Create Customer Modal -->
<div id="createModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Đăng Ký Khách Hàng Mới</h3>
            <button onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.customers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tên tài khoản *</label>
                    <input type="text" name="username" required placeholder="customer_lan" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email liên lạc *</label>
                    <input type="email" name="email" required placeholder="lan.customer@mail.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Số điện thoại *</label>
                    <input type="text" name="phone" required placeholder="091XXXXXXX" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mật khẩu *</label>
                    <input type="password" name="password" required placeholder="Mật khẩu tài khoản" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cấp độ hội viên *</label>
                    <select name="membership_level" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <option value="Standard">Standard (Hội viên thường)</option>
                        <option value="Silver">Silver (Bạc)</option>
                        <option value="Gold">Gold (Vàng)</option>
                        <option value="VIP">⭐ VIP</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Điểm tích lũy ban đầu *</label>
                    <input type="number" name="points" required placeholder="0" value="0" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700">Đăng ký thành viên</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Customer Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Cập Nhật Hồ Sơ Hội Viên</h3>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editCustomerForm" method="POST" class="p-6 space-y-4">
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
                    <input type="password" name="password" placeholder="Đổi mật khẩu nếu cần" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-3">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cấp độ hội viên *</label>
                    <select id="edit_membership_level" name="membership_level" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                        <option value="Standard">Standard (Hội viên thường)</option>
                        <option value="Silver">Silver (Bạc)</option>
                        <option value="Gold">Gold (Vàng)</option>
                        <option value="VIP">⭐ VIP</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Điểm tích lũy *</label>
                    <input type="number" id="edit_points" name="points" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
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

    function openEditModal(customer, user) {
        document.getElementById('edit_username').value = user.username || '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_membership_level').value = customer.membership_level;
        document.getElementById('edit_points').value = customer.points;

        const form = document.getElementById('editCustomerForm');
        form.action = `/admin/customers/${customer.id}`;

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection
