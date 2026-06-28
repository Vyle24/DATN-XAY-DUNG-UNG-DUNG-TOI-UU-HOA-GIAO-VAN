@extends('layouts.customer')

@section('header_title', 'Mã Giảm Giá Khuyến Mãi')

@section('content')
<div class="mb-6">
    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Mã Giảm Giá Của Bạn</h3>
    <p class="text-xs text-slate-400 mt-1">Sử dụng các mã giảm giá dưới đây khi đặt đơn giao hàng để tiết kiệm tối đa chi phí vận chuyển.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    @forelse($vouchers as $v)
    @php
        $isExpired = $v->expiry_date && $v->expiry_date->isPast();
    @endphp
    <div class="relative bg-white border border-slate-200/60 rounded-3xl p-5 shadow-sm overflow-hidden flex flex-col justify-between {{ $isExpired ? 'opacity-65' : '' }}">
        <!-- Left & Right punch holes (coupon effect) -->
        <div class="absolute top-1/2 -left-3 h-6 w-6 rounded-full bg-slate-50 border-r border-slate-200/60 -translate-y-1/2"></div>
        <div class="absolute top-1/2 -right-3 h-6 w-6 rounded-full bg-slate-50 border-l border-slate-200/60 -translate-y-1/2"></div>

        <div>
            <!-- Header -->
            <div class="flex items-center justify-between mb-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-extrabold uppercase {{ $isExpired ? 'bg-slate-100 text-slate-500' : 'bg-indigo-50 text-indigo-700' }}">
                    Voucher Giao Hàng
                </span>
                @if($isExpired)
                <span class="text-[9px] font-bold text-rose-500">Đã hết hạn</span>
                @else
                <span class="text-[9px] font-bold text-emerald-500 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Còn hiệu lực
                </span>
                @endif
            </div>

            <!-- Discount amount -->
            <div class="mb-3">
                <h4 class="text-2xl font-black text-slate-800">Giảm {{ $v->discount_percent }}%</h4>
                <p class="text-[10px] text-slate-400 mt-0.5">Tối đa {{ number_format($v->max_discount) }}đ</p>
            </div>
        </div>

        <div class="border-t border-dashed border-slate-200 pt-4 mt-2">
            <div class="flex justify-between items-center">
                <div>
                    <span class="text-[9px] font-bold text-slate-450 uppercase block">Mã giảm giá</span>
                    <span class="font-mono font-extrabold text-slate-800 text-xs tracking-wider uppercase" id="code-{{ $v->id }}">{{ $v->code }}</span>
                </div>
                <div>
                    @if(!$isExpired)
                    <button onclick="copyCode('{{ $v->code }}', this)" class="inline-flex items-center justify-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold text-[10px] rounded-xl transition">
                        Sao chép mã
                    </button>
                    @else
                    <button class="inline-flex items-center justify-center px-3 py-1.5 bg-slate-100 text-slate-400 font-bold text-[10px] rounded-xl cursor-not-allowed" disabled>
                        Không dùng được
                    </button>
                    @endif
                </div>
            </div>
            
            <div class="mt-3 flex justify-between items-center text-[10px] text-slate-450">
                <span>Hạn sử dụng:</span>
                <span class="font-semibold">{{ $v->expiry_date ? $v->expiry_date->format('d/m/Y') : 'Không giới hạn' }}</span>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-3xl border border-slate-200/60 p-12 text-center text-slate-400 shadow-sm">
        <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-3">
            <svg class="h-6 w-6 text-slate-450" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
        </div>
        Hiện không có mã giảm giá nào được phát hành.
    </div>
    @endforelse
</div>
@endsection

@section('scripts')
<script>
    function copyCode(code, btn) {
        navigator.clipboard.writeText(code).then(() => {
            const originalText = btn.innerText;
            btn.innerText = "Đã sao chép! ✓";
            btn.classList.remove("bg-blue-50", "text-blue-600");
            btn.classList.add("bg-emerald-500", "text-white");
            
            setTimeout(() => {
                btn.innerText = originalText;
                btn.classList.remove("bg-emerald-500", "text-white");
                btn.classList.add("bg-blue-50", "text-blue-600");
            }, 1500);
        });
    }
</script>
@endsection
