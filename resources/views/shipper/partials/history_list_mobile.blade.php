@forelse($historyOrders as $order)
    <div class="p-4 space-y-3 hover:bg-slate-50/20 transition">
        <div class="flex items-center justify-between">
            <span class="font-bold text-blue-600 font-mono text-xs">{{ $order->order_code }}</span>
            <span class="text-[10px] text-slate-400 font-medium">
                {{ \Carbon\Carbon::parse($order->updated_at)->format('H:i d/m/Y') }}
            </span>
        </div>
        
        <div class="grid grid-cols-2 gap-2 text-xs">
            <div>
                <span class="text-slate-400 block text-[9px] uppercase font-bold">Khách hàng</span>
                <span class="font-bold text-slate-800">{{ $order->customer->user->username ?? 'Khách lẻ' }}</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $order->customer->user->phone ?? '' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[9px] uppercase font-bold">Tiền COD</span>
                <span class="font-bold text-slate-800 text-xs">{{ number_format($order->shipping_fee) }}đ</span>
            </div>
        </div>

        <div class="text-xs space-y-1 bg-slate-50 p-2.5 rounded-xl border border-slate-150">
            <div class="flex items-center justify-between pt-1">
                <span class="text-[10px] text-slate-400 font-medium">Trạng thái:</span>
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
            </div>
        </div>
    </div>
@empty
    <div id="mobile-empty-state" class="p-6 text-center text-slate-400 text-xs">Không có dữ liệu lịch sử giao hàng.</div>
@endforelse
