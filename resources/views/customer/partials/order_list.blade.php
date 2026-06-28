@forelse($recentOrders as $order)
<tr class="hover:bg-slate-50/20 transition order-item">
    <td class="px-6 py-4 font-bold text-blue-600">
        {{ $order->order_code }}
    </td>
    <td class="px-6 py-4 text-slate-400">
        {{ $order->created_at->format('d/m/Y H:i') }}
    </td>
    <td class="px-6 py-4 font-medium text-slate-800 max-w-xs truncate">
        {{ $order->delivery_address }}
    </td>
    <td class="px-6 py-4 font-bold text-slate-700">
        {{ number_format($order->shipping_fee) }}đ
    </td>
    <td class="px-6 py-4">
        <span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-semibold text-slate-600">{{ $order->payment_method ?? 'COD' }}</span>
    </td>
    <td class="px-6 py-4 text-center">
        @if($order->status === 'pending')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Chờ xử lý</span>
        @elseif($order->status === 'processing')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">Đang giao</span>
        @elseif($order->status === 'delivered')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Thành công</span>
        @elseif($order->status === 'cancelled')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Đã hủy</span>
        @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">Thất bại</span>
        @endif
    </td>
    <td class="px-6 py-4 text-right">
        <div class="flex items-center justify-end gap-2">
            @if($order->status === 'pending' && $order->payment_status !== 'paid')
                <a href="{{ route('customer.payment.checkout', $order->id) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white transition rounded-xl text-[10px] font-bold shadow-sm shadow-blue-600/20">
                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Thanh toán
                </a>
            @endif
            
            <a href="{{ route('customer.orders.track', $order->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-50 border border-indigo-100 text-indigo-700 hover:bg-indigo-100 transition rounded-xl text-[10px] font-bold">
                <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" /></svg>
                Xem hành trình
            </a>
            @if($order->status === 'pending')
            <form action="{{ route('customer.orders.cancel', $order->id) }}" method="POST"
                onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng {{ $order->order_code }}?')">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-rose-50 border border-rose-100 text-rose-600 hover:bg-rose-100 transition rounded-xl text-[10px] font-bold">
                    <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    Hủy đơn
                </button>
            </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr id="empty-state">
    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
        <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
            <svg class="h-6 w-6 text-slate-450" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
        </div>
        Bạn chưa có đơn đặt hàng nào trong danh sách.
    </td>
</tr>
@endforelse
