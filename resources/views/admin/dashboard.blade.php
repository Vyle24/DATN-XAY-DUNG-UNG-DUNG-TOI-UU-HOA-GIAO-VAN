@extends('layouts.admin')

@section('header_title', 'Bảng Thống Kê Tổng Quan')

@section('content')
<!-- Stats Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stat 1: Total Orders -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
        <div>
            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Tổng đơn hàng</span>
            <span class="text-3xl font-extrabold text-slate-900">{{ $totalOrders }}</span>
            <span class="block text-xs text-blue-500 font-semibold mt-1">Hệ thống ghi nhận</span>
        </div>
        <div class="h-12 w-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-inner">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
        </div>
    </div>

    <!-- Stat 2: Processing Orders -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
        <div>
            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Đơn đang giao</span>
            <span class="text-3xl font-extrabold text-amber-600">{{ $processingOrders }}</span>
            <span class="block text-xs text-amber-500 font-semibold mt-1">Đang vận chuyển</span>
        </div>
        <div class="h-12 w-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-inner">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
    </div>

    <!-- Stat 3: Active Shippers -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm hover:shadow-md transition duration-200 flex items-center justify-between">
        <div>
            <span class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Shipper hoạt động</span>
            <span class="text-3xl font-extrabold text-emerald-600">{{ $activeShippersCount }}</span>
            <span class="block text-xs text-emerald-500 font-semibold mt-1">Sẵn sàng vận chuyển</span>
        </div>
        <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-inner">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
    </div>
</div>

<!-- Charts Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Chart: Order Status Distribution -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm lg:col-span-1">
        <h3 class="text-base font-bold text-slate-800 mb-4">Trạng thái đơn hàng</h3>
        <div class="relative h-64 flex items-center justify-center">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Chart: Logistics Revenue Graph -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/60 shadow-sm lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-800">Biểu đồ phí vận chuyển (Ước tính)</h3>
            <span class="text-xs text-slate-400">7 ngày gần nhất</span>
        </div>
        <div class="relative h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<!-- Grid bottom: Shipper status & recent orders -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Recent Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden xl:col-span-2 flex flex-col justify-between">
        <div>
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-800">Đơn hàng mới nhận</h3>
                    <p class="text-xs text-slate-450 mt-0.5">Giám sát các đơn hàng vừa đưa lên hệ thống</p>
                </div>
                <a href="{{ route('admin.orders') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                    Xem tất cả
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-6 py-4">Mã Đơn</th>
                            <th class="px-6 py-4">Khách Hàng</th>
                            <th class="px-6 py-4">Shipper</th>
                            <th class="px-6 py-4">Giao Đến</th>
                            <th class="px-6 py-4 text-center">Trạng Thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($orders->take(5) as $order)
                        <tr class="hover:bg-slate-50/30 transition">
                            <td class="px-6 py-3.5 font-bold text-blue-600">
                                {{ $order->order_code }}
                            </td>
                            <td class="px-6 py-3.5">
                                <div class="font-semibold text-slate-900">{{ $order->customer->user->username ?? 'Khách lẻ' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $order->customer->user->phone ?? '' }}</div>
                            </td>
                            <td class="px-6 py-3.5">
                                @if($order->shipper)
                                    <span class="font-semibold text-slate-800">{{ $order->shipper->user->username ?? '' }}</span>
                                @else
                                    <span class="text-slate-400 italic">Chưa gán</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 truncate max-w-[180px]" title="{{ $order->delivery_address }}">
                                {{ $order->delivery_address }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @if($order->status === 'delivered')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">Đã giao</span>
                                @elseif($order->status === 'processing')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-100">Đang giao</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">Chờ xử lý</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-450">Không có đơn hàng nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active Shippers List -->
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6 xl:col-span-1">
        <h3 class="text-base font-bold text-slate-800 mb-4">Trạng thái đội ngũ Shipper</h3>
        <div class="space-y-4">
            @forelse($shippersList as $ship)
                <div class="flex items-center justify-between p-3.5 bg-slate-50/60 border border-slate-100 rounded-xl hover:border-slate-200 transition">
                    <div class="flex items-center space-x-3">
                        <div class="h-9 w-9 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 flex items-center justify-center">
                            <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-800">{{ $ship->user->username ?? '' }}</span>
                            <span class="block text-[10px] text-slate-400">{{ $ship->license_no }} • {{ $ship->vehicle_type }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        @if($ship->is_active)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-100 text-emerald-800">Online</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-200 text-slate-600">Offline</span>
                        @endif
                        <span class="block text-[10px] text-slate-500 mt-1">{{ number_format($ship->wallet_balance) }}đ</span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-450 text-center py-6">Không tìm thấy shipper nào.</p>
            @endforelse
        </div>
    </div>
</div>

<!-- ChartJS Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Status Doughnut Chart
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Chờ xử lý', 'Đang giao', 'Đã giao'],
                datasets: [{
                    data: [
                        {{ $chartData['pending'] }},
                        {{ $chartData['processing'] }},
                        {{ $chartData['delivered'] }}
                    ],
                    backgroundColor: ['#3b82f6', '#f59e0b', '#10b981'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: { family: 'Outfit', size: 11, weight: '500' },
                            padding: 15
                        }
                    }
                },
                cutout: '65%'
            }
        });

        // Revenue Bar Chart
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                datasets: [{
                    label: 'Doanh thu ship (đ)',
                    data: [120000, 150000, 180000, 140000, 210000, 250000, 190000],
                    backgroundColor: 'rgba(59, 130, 246, 0.85)',
                    hoverBackgroundColor: '#3b82f6',
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Outfit', size: 10 },
                            callback: function(value) { return value.toLocaleString() + 'đ'; }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Outfit', size: 10 } }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection
