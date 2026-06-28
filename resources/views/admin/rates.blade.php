@extends('layouts.admin')

@section('header_title', 'Cấu Hình Biểu Phí')

@section('content')

<!-- Statistics Cards Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng số vùng thiết lập</span>
        <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Quận/Huyện cấu hình</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Cước cơ bản TB</span>
        <span class="text-xl font-extrabold text-blue-600 mt-1 block">{{ number_format($stats['avg_base_price']) }}đ</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Giá mở cửa bình quân</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đơn giá km trung bình</span>
        <span class="text-xl font-extrabold text-emerald-600 mt-1 block">{{ number_format($stats['avg_price_per_km']) }}đ</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Mỗi km tiếp theo</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đơn giá km Cao nhất</span>
        <span class="text-xl font-extrabold text-rose-600 mt-1 block">{{ number_format($stats['max_price_per_km']) }}đ</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Khu vực cao điểm/xa</span>
    </div>
</div>

<!-- Global Pricing Settings Card -->
<div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm mb-6">
    <div class="flex items-center space-x-3 mb-4">
        <div class="p-2 rounded-xl bg-blue-50 text-blue-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        </div>
        <div>
            <h4 class="font-bold text-slate-800 text-sm">Cấu Hình Tính Phí Vận Chuyển Toàn Cục</h4>
            <p class="text-[11px] text-slate-400 font-medium">Quy định khối lượng cơ bản và phụ phí cho mỗi KG phụ trội</p>
        </div>
    </div>
    <form action="{{ route('admin.settings.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        @csrf
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Khối lượng cơ bản (kg)</label>
            <input type="number" step="0.1" name="base_weight_limit" value="{{ $settings['base_weight_limit'] ?? 2.0 }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Đơn giá mỗi KG phụ trội (đ/kg)</label>
            <input type="number" name="price_per_kg" value="{{ $settings['price_per_kg'] ?? 5000 }}" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <button type="submit" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-md shadow-blue-500/10 transition flex items-center justify-center gap-1.5">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Lưu cấu hình toàn cục
            </button>
        </div>
    </form>
</div>

<!-- Header Controls -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h3 class="text-lg font-bold text-slate-800">Biểu Phí Vận Chuyển Quận Huyện</h3>
        <p class="text-sm text-slate-500 font-medium">Quy định mức giá cơ bản và phụ phí quãng đường làm căn cứ tính tiền tự động của đơn hàng</p>
    </div>
    <div>
        <button onclick="openCreateModal()" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-sm font-semibold rounded-xl text-white shadow-md shadow-blue-600/10 transition duration-150">
            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            Thiết lập biểu phí mới
        </button>
    </div>
</div>

<!-- Search & Filters Form -->
<form action="{{ route('admin.rates') }}" method="GET" class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-4 grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tìm kiếm khu vực</label>
        <div class="relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên Quận/Huyện..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            @if(request('search'))
                <a href="{{ route('admin.rates') }}" class="absolute right-3 top-2.5 text-slate-400 hover:text-slate-650">✕</a>
            @endif
        </div>
    </div>

    <div>
        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sắp xếp biểu phí</label>
        <select name="sort" onchange="this.form.submit()" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-xs focus:bg-white focus:outline-none">
            <option value="name_asc" {{ request('sort') === 'name_asc' ? 'selected' : '' }}>Tên khu vực (A - Z)</option>
            <option value="base_desc" {{ request('sort') === 'base_desc' ? 'selected' : '' }}>Cước cơ bản (Cao - Thấp)</option>
            <option value="base_asc" {{ request('sort') === 'base_asc' ? 'selected' : '' }}>Cước cơ bản (Thấp - Cao)</option>
            <option value="km_desc" {{ request('sort') === 'km_desc' ? 'selected' : '' }}>Đơn giá / km (Cao - Thấp)</option>
            <option value="km_asc" {{ request('sort') === 'km_asc' ? 'selected' : '' }}>Đơn giá / km (Thấp - Cao)</option>
        </select>
    </div>

    <div class="flex items-end">
        <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-sm transition">
            Lọc kết quả
        </button>
    </div>
</form>

<!-- Rates Table Card -->
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Quận / Huyện Đơn Vị</th>
                    <th class="px-6 py-4">Cước Phí Cơ Bản (Base)</th>
                    <th class="px-6 py-4">Đơn Giá Mỗi Kilomet (per KM)</th>
                    <th class="px-6 py-4 text-right">Hành Động</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                @forelse($rates as $rate)
                <tr class="hover:bg-slate-50/20 transition">
                    <td class="px-6 py-4 font-bold text-slate-550">#{{ $rate->id }}</td>
                    <td class="px-6 py-4 font-bold text-slate-900">{{ $rate->district_name }}</td>
                    <td class="px-6 py-4 font-semibold text-slate-800">{{ number_format($rate->base_price) }}đ</td>
                    <td class="px-6 py-4 font-semibold text-blue-600">{{ number_format($rate->price_per_km) }}đ / km</td>
                    <td class="px-6 py-4 text-right text-xs font-bold space-x-2">
                        <button onclick="openEditModal({{ json_encode($rate) }})" class="text-blue-600 hover:text-blue-800 hover:underline">Sửa</button>
                        
                        <form action="{{ route('admin.rates.destroy', $rate->id) }}" method="POST" class="inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa biểu phí giao hàng của quận {{ $rate->district_name }} không?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-rose-600 hover:text-rose-800 hover:underline">Xóa</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">Không tìm thấy cấu hình biểu phí phù hợp.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($rates->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $rates->links() }}
    </div>
    @endif
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Create Rate Modal -->
<div id="createModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Cấu Hình Biểu Phí Mới</h3>
            <button onclick="closeModal('createModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('admin.rates.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tên Quận / Huyện *</label>
                <input type="text" name="district_name" required placeholder="Quận Cầu Giấy, Huyện Gia Lâm..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cước cơ bản (đ) *</label>
                    <input type="number" name="base_price" required placeholder="10000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Đơn giá / km (đ) *</label>
                    <input type="number" name="price_per_km" required placeholder="3000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700">Thiết lập</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Edit Rate Modal -->
<div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl border border-slate-100">
        <div class="px-6 py-4 border-b border-slate-150 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-800">Cập Nhật Biểu Phí</h3>
            <button onclick="closeModal('editModal')" class="text-slate-400 hover:text-slate-650">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="editRateForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tên Quận / Huyện *</label>
                <input type="text" id="edit_district_name" name="district_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Cước cơ bản (đ) *</label>
                    <input type="number" id="edit_base_price" name="base_price" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Đơn giá / km (đ) *</label>
                    <input type="number" id="edit_price_per_km" name="price_per_km" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:bg-white focus:outline-none">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-4 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-sm font-semibold rounded-xl text-slate-500 bg-slate-100 hover:bg-slate-200">Hủy</button>
                <button type="submit" class="px-5 py-2 text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 font-bold">Cập nhật biểu phí</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function openEditModal(rate) {
        document.getElementById('edit_district_name').value = rate.district_name;
        document.getElementById('edit_base_price').value = Math.round(rate.base_price);
        document.getElementById('edit_price_per_km').value = Math.round(rate.price_per_km);

        const form = document.getElementById('editRateForm');
        form.action = `/admin/rates/${rate.id}`;

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection
