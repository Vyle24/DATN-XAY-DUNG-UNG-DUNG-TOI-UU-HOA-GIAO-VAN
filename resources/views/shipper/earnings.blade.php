@extends('layouts.shipper')

@section('content')
<div class="space-y-6">

    {{-- Header Stats --}}
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-xl shadow-blue-500/15">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-extrabold flex items-center gap-1.5">
                    <svg class="h-5 w-5 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Lịch Sử Thu Nhập</span>
                </h3>
                <p class="text-xs text-blue-100 mt-1">Theo dõi thu nhập chi tiết từ các đơn hàng đã giao thành công</p>
            </div>
            <a href="{{ route('shipper.profile') }}" class="inline-flex items-center text-xs font-bold bg-white/20 hover:bg-white/30 transition px-4 py-2 rounded-xl">
                ← Quay lại hồ sơ
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Tổng thu nhập</span>
            <span class="text-xl font-extrabold text-blue-600 mt-1 block">{{ number_format($stats['total_earnings']) }}đ</span>
            <div class="mt-2 text-[9px] text-slate-400">Từ tất cả đơn đã giao</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">Đơn đã giao</span>
            <span class="text-xl font-extrabold text-emerald-600 mt-1 block">{{ $stats['total_delivered'] }}</span>
            <div class="mt-2 text-[9px] text-emerald-600 font-bold">Hoàn thành xuất sắc</div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-slate-200/60 shadow-sm">
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">TB mỗi đơn</span>
            <span class="text-xl font-extrabold text-slate-800 mt-1 block">{{ number_format($stats['avg_per_order']) }}đ</span>
            <div class="mt-2 text-[9px] text-slate-400">Phí giao trung bình</div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-4 text-white shadow-sm">
            <span class="text-[10px] text-blue-100 font-bold uppercase tracking-wider block">Số dư ví</span>
            <span class="text-xl font-extrabold mt-1 block">{{ number_format($stats['wallet_balance']) }}đ</span>
            <div class="mt-2 text-[9px] text-blue-200">Ví tài khoản hiện tại</div>
        </div>
    </div>

    {{-- Earnings History Table --}}
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h4 class="font-bold text-slate-800 text-sm">Lịch sử đơn hàng đã giao</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">Tất cả đơn hàng bạn đã giao thành công</p>
            </div>
            <form id="earningsFilterForm" class="flex items-center gap-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                <span class="text-xs text-slate-400">-</span>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition">
                <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-xl hover:bg-blue-700 transition">Lọc</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <th class="px-6 py-4">Mã đơn hàng</th>
                        <th class="px-6 py-4">Khách hàng</th>
                        <th class="px-6 py-4">Địa chỉ giao</th>
                        <th class="px-6 py-4 text-right">Phí nhận được</th>
                        <th class="px-6 py-4 text-right">Thời gian giao</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($deliveredOrders as $order)
                    <tr class="hover:bg-slate-50/20 transition">
                        <td class="px-6 py-4 font-bold text-blue-600 font-mono">{{ $order->order_code }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $order->customer->user->username ?? 'Khách lẻ' }}</div>
                            <div class="text-[10px] text-slate-400">{{ $order->customer->user->phone ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 max-w-xs">
                            <div class="font-medium text-slate-800 truncate" title="{{ $order->delivery_address }}">{{ $order->delivery_address }}</div>
                            <div class="text-[10px] text-slate-400 mt-0.5 truncate">Từ: {{ $order->pickup_address }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-extrabold text-emerald-600">+{{ number_format($order->shipping_fee) }}đ</span>
                        </td>
                        <td class="px-6 py-4 text-right text-[10px] text-slate-400 font-medium">
                            {{ \Carbon\Carbon::parse($order->updated_at)->format('H:i d/m/Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            Chưa có đơn hàng nào được giao thành công.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($deliveredOrders->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            {{ $deliveredOrders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
