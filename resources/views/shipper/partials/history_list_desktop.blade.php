@forelse($historyOrders as $order)
    <tr class="hover:bg-slate-50/20 transition">
        <td class="px-6 py-4">
            <span class="font-bold text-blue-600 font-mono">{{ $order->order_code }}</span>
        </td>
        <td class="px-6 py-4">
            <div class="font-bold text-slate-800">{{ $order->customer->user->username ?? 'Khách lẻ' }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">{{ $order->customer->user->phone ?? '' }}</div>
        </td>
        <td class="px-6 py-4 max-w-[200px]">
            <div class="font-bold text-slate-700 text-[11px] truncate" title="{{ $order->delivery_address }}">Giao: {{ $order->delivery_address }}</div>
            <div class="text-[9px] text-slate-400 mt-0.5 truncate" title="{{ $order->pickup_address }}">Từ: {{ $order->pickup_address }}</div>
        </td>
        <td class="px-6 py-4 font-bold text-slate-800">{{ number_format($order->shipping_fee) }}đ</td>
        <td class="px-6 py-4 text-center">
            @if($order->status === 'delivered')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                    Thành công
                </span>
            @elseif($order->status === 'failed')
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                    Thất bại
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-100 text-slate-650 border border-slate-200">
                    {{ $order->status }}
                </span>
            @endif
        </td>
        <td class="px-6 py-4 text-right text-[10px] text-slate-400 font-medium">
            {{ \Carbon\Carbon::parse($order->updated_at)->format('H:i d/m/Y') }}
        </td>
    </tr>
@empty
    <tr id="desktop-empty-state">
        <td colspan="6" class="px-6 py-12 text-center text-slate-400">Không có dữ liệu lịch sử giao hàng.</td>
    </tr>
@endforelse
