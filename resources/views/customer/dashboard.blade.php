@extends('layouts.customer')

@section('header_title', 'Trang Tổng Quan')

@section('content')
<!-- Welcome banner -->
<div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-6 md:p-8 text-white shadow-xl shadow-blue-500/10 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h3 class="text-lg md:text-xl font-bold">Xin chào, {{ $customer->user->username ?? 'Khách hàng' }}! 👋</h3>
        <p class="text-xs text-blue-100 mt-1 max-w-md">Chào mừng bạn quay lại hệ thống tối ưu hóa giao vận. Đặt đơn hàng mới nhanh chóng và theo dõi hành trình thời gian thực ngay bên dưới.</p>
    </div>
    <div>
        <a href="{{ route('customer.orders.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-white text-blue-600 hover:bg-blue-50 transition font-bold text-xs rounded-xl shadow-sm">
            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            Đặt đơn giao hàng ngay
        </a>
    </div>
</div>

<!-- Stats cards Grid -->
<div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng đơn hàng</span>
        <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ $stats['total'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Tất cả trạng thái</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Chờ xử lý</span>
        <span class="text-xl font-extrabold text-amber-600 mt-1 block">{{ $stats['pending'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Chờ điều phối</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Thành công</span>
        <span class="text-xl font-extrabold text-emerald-600 mt-1 block">{{ $stats['delivered'] }}</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Tỉ lệ: {{ $stats['success_rate'] }}%</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đã chi tiêu</span>
        <span class="text-xl font-extrabold text-blue-600 mt-1 block">{{ number_format($stats['total_spent']) }}đ</span>
        <span class="text-[10px] text-slate-400 mt-1 block">Tổng phí vận chuyển</span>
    </div>

    <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Điểm tích lũy</span>
        <span class="text-xl font-extrabold text-indigo-600 mt-1 block">{{ number_format($customer->points ?? 0) }}</span>
        <span class="text-[10px] text-indigo-500 mt-1 block font-semibold">Hạng {{ $customer->membership_level ?? 'Standard' }}</span>
    </div>
</div>



<!-- Order History List -->
<div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden mb-6">
    <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h4 class="font-bold text-slate-800 text-sm">Danh Sách Đơn Hàng Của Bạn</h4>
            <p class="text-[11px] text-slate-400 font-medium">Theo dõi và quản lý lịch sử vận đơn</p>
        </div>
        <div class="flex items-center gap-2">
            <form id="filterForm" class="flex items-center gap-2">
                <input type="text" name="search" id="searchInput" placeholder="Tìm theo mã đơn..." value="{{ request('search') }}" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 w-48 transition">
                <select name="status" id="statusSelect" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang giao</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Thành công</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Thất bại</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </form>
            <a href="{{ route('customer.orders.create') }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition whitespace-nowrap">
                + Đặt đơn mới
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-250/60 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <th class="px-6 py-4">Mã đơn</th>
                    <th class="px-6 py-4">Ngày đặt</th>
                    <th class="px-6 py-4">Địa chỉ giao</th>
                    <th class="px-6 py-4">Phí vận chuyển</th>
                    <th class="px-6 py-4">Hình thức</th>
                    <th class="px-6 py-4 text-center">Trạng thái</th>
                    <th class="px-6 py-4 text-right">Hành động</th>
                </tr>
            </thead>
            <tbody id="orderList" class="divide-y divide-slate-100 text-xs text-slate-650">
                @include('customer.partials.order_list', ['recentOrders' => $recentOrders])
            </tbody>
        </table>
    </div>

    @if($recentOrders->hasMorePages())
    <div class="px-6 py-4 border-t border-slate-100 text-center" id="loadMoreContainer">
        <button id="loadMoreBtn" data-page="2" class="inline-flex items-center justify-center px-5 py-2 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition">
            Tải thêm lịch sử
        </button>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const orderList = document.getElementById('orderList');
        const filterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchInput');
        const statusSelect = document.getElementById('statusSelect');
        const loadMoreContainer = document.getElementById('loadMoreContainer');
        let isLoading = false;

        // Auto submit filter on change
        let debounceTimer;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => submitFilter(), 500);
        });
        statusSelect.addEventListener('change', () => submitFilter());

        function submitFilter() {
            const url = new URL(window.location.href);
            url.searchParams.set('search', searchInput.value);
            url.searchParams.set('status', statusSelect.value);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }

        // Lazy load
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
                url.searchParams.set('search', searchInput.value);
                url.searchParams.set('status', statusSelect.value);

                fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.html) {
                        orderList.insertAdjacentHTML('beforeend', data.html);
                    }
                    if(data.hasMorePages) {
                        loadMoreBtn.setAttribute('data-page', parseInt(page) + 1);
                    } else {
                        loadMoreContainer.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);
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
@endsection
