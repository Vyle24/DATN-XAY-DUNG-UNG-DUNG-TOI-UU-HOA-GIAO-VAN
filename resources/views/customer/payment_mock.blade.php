@extends('layouts.customer')

@section('header_title', 'Thanh Toán VNPay')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
        <!-- VNPay Header Mock -->
        <div class="bg-gradient-to-r from-blue-700 to-blue-500 p-6 flex justify-between items-center text-white">
            <div>
                <h2 class="text-xl font-bold">Cổng Thanh Toán VNPay</h2>
                <p class="text-sm text-blue-100 mt-1">Môi trường thử nghiệm (Sandbox)</p>
            </div>
            <div class="bg-white p-2 rounded-lg">
                <span class="font-black text-blue-600 text-xl tracking-tighter">VNPAY</span>
            </div>
        </div>

        <div class="p-8">
            <div class="text-center mb-8">
                <p class="text-sm text-slate-500 font-medium">Đơn vị nhận tiền</p>
                <h3 class="text-lg font-bold text-slate-800">CÔNG TY GIAO VẬN TỐI ƯU</h3>
            </div>

            <div class="space-y-4 mb-8">
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <span class="text-sm text-slate-500">Mã đơn hàng</span>
                    <span class="font-bold text-slate-800">{{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <span class="text-sm text-slate-500">Người thanh toán</span>
                    <span class="font-semibold text-slate-800">{{ $order->customer->user->username ?? 'Khách hàng' }}</span>
                </div>
                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                    <span class="text-sm text-slate-500">Nội dung thanh toán</span>
                    <span class="font-medium text-slate-800 text-right max-w-xs">Thanh toán cước phí giao hàng cho đơn {{ $order->order_code }}</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-base font-bold text-slate-800">Tổng tiền thanh toán</span>
                    <span class="text-2xl font-black text-blue-600">{{ number_format($order->shipping_fee) }} <span class="text-sm font-bold text-blue-600">VND</span></span>
                </div>
            </div>

            <!-- Mock Action Buttons -->
            <form action="{{ route('customer.payment.process', $order->id) }}" method="POST" class="space-y-4">
                @csrf
                <p class="text-xs text-center text-slate-400 mb-4">(Đây là màn hình mô phỏng cổng thanh toán VNPay)</p>
                
                <button type="submit" name="status" value="success" class="w-full flex items-center justify-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 transition">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Xác nhận thanh toán thành công
                </button>
                
                <button type="submit" name="status" value="cancel" class="w-full flex items-center justify-center px-6 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 font-semibold rounded-xl transition">
                    Hủy giao dịch & Quay lại
                </button>
            </form>
        </div>
        
        <div class="bg-slate-50 p-4 text-center text-[10px] text-slate-400 border-t border-slate-100">
            Powered by VNPay Sandbox &copy; {{ date('Y') }}
        </div>
    </div>
</div>
@endsection
