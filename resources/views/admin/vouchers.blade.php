@extends('layouts.admin')

@section('header_title', 'Khuyến Mãi & Vouchers')

@section('content')

<!-- Statistics Cards Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng số mã Voucher</span>
        <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Mã đã phát hành</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Voucher đang hoạt động</span>
        <span class="text-xl font-extrabold text-emerald-600 mt-1 block">{{ $stats['active'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Chưa quá ngày hết hạn</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Voucher Hết Hạn</span>
        <span class="text-xl font-extrabold text-slate-400 mt-1 block">{{ $stats['expired'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Đã vô hiệu lực</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tỷ lệ giảm trung bình</span>
        <span class="text-xl font-extrabold text-blue-600 mt-1 block">{{ number_format($stats['avg_discount'], 1) }}%</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Mức chiết khấu trung bình</span>
    </div>
</div>

<!-- Header Controls -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h3 class="text-lg font-bold text-slate-800">Quản Lý Mã Khuyến Mãi (Vouchers)</h3>
        <p class="text-sm text-slate-500 font-medium">Phát hành, sửa đổi hoặc thu hồi mã giảm giá cước phí vận chuyển toàn sàn</p>
    </div>
    <div>
        <button onclick="openCreateModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-sm font-semibold rounded-xl text-white shadow-md shadow-blue-600/10 transition duration-150">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Phát hành Voucher mới
        </button>
    </div>
</div>

<!-- Search & Filters Form -->
<form action="{{ route('admin.vouchers') }}" method="GET" class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Mã Voucher</label>
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập mã voucher..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @if(request('search'))
                <a href="{{ route('admin.vouchers') }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-650">✕</a>
            @endif
        </div>
    </div>

    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Trạng thái hạn dùng</label>
        <select name="status" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang hoạt động (Còn hạn)</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Hết hạn sử dụng</option>
        </select>
    </div>

    <div class="flex items-end">
        <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-sm transition">
            Lọc mã ưu đãi
        </button>
    </div>
</form>

<!-- Vouchers Table Card -->
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-6 py-4">Mã Voucher</th>
                    <th class="px-6 py-4">Tỷ Lệ Giảm Giá</th>
                    <th class="px-6 py-4">Giảm Tối Đa</th>
                    <th class="px-6 py-4">Ngày Hết Hạn</th>
                    <th class="px-6 py-4 text-center">Trạng Thái</th>
                    <th class="px-6 py-4 text-right">Hành Động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @forelse($vouchers as $voucher)
                <tr class="hover:bg-slate-50/20 transition">
                    <td class="px-6 py-4 font-bold text-blue-600 font-mono text-sm tracking-wider">{{ $voucher->code }}</td>
                    <td class="px-6 py-4 font-semibold text-slate-800">{{ $voucher->discount_percent }}%</td>
                    <td class="px-6 py-4 font-semibold text-slate-900">{{ number_format($voucher->max_discount) }}đ</td>
                    <td class="px-6 py-4 text-slate-600 font-medium">{{ \Carbon\Carbon::parse($voucher->expiry_date)->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        @if(\Carbon\Carbon::parse($voucher->expiry_date)->isPast())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200">Hết Hạn</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Hoạt Động</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right text-xs font-bold space-x-2">
                        <button onclick="openEditModal({{ json_encode($voucher) }})" class="text-blue-600 hover:text-blue-800 hover:underline">Sửa</button>
                        
                        <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy bỏ mã voucher {{ $voucher->code }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 hover:underline">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">Không tìm thấy mã ưu đãi nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($vouchers->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $vouchers->links() }}
    </div>
    @endif
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Create Voucher Modal -->
<div id="createModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Phát Hành Mã Voucher Mới</h3>
            <button onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.vouchers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mã Voucher (Ví dụ: GIAONHANH20) *</label>
                <input type="text" name="code" required placeholder="GIAONHANH20" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tỷ lệ giảm (%) *</label>
                    <input type="number" name="discount_percent" min="1" max="100" required placeholder="20" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Giảm tối đa (đ) *</label>
                    <input type="number" name="max_discount" required placeholder="50000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ngày hết hạn *</label>
                <input type="date" name="expiry_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700">Phát hành</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Voucher Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Cập Nhật Voucher</h3>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editVoucherForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mã Voucher *</label>
                <input type="text" id="edit_code" name="code" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-mono focus:bg-white focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tỷ lệ giảm (%) *</label>
                    <input type="number" id="edit_discount_percent" min="1" max="100" name="discount_percent" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Giảm tối đa (đ) *</label>
                    <input type="number" id="edit_max_discount" name="max_discount" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ngày hết hạn *</label>
                <input type="date" id="edit_expiry_date" name="expiry_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 font-bold">Cập nhật Voucher</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function openEditModal(voucher) {
        document.getElementById('edit_code').value = voucher.code;
        document.getElementById('edit_discount_percent').value = voucher.discount_percent;
        document.getElementById('edit_max_discount').value = Math.round(voucher.max_discount);
        document.getElementById('edit_expiry_date').value = voucher.expiry_date;

        const form = document.getElementById('editVoucherForm');
        form.action = `/admin/vouchers/${voucher.id}`;

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection
