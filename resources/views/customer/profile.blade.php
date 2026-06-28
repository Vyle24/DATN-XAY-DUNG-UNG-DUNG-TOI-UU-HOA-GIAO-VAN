@extends('layouts.customer')

@section('header_title', 'Hồ Sơ Cá Nhân')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Left Profile card -->
    <div class="lg:col-span-4 space-y-6">
        <!-- Membership Badge Info -->
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6 text-center">
            <span class="h-16 w-16 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 flex items-center justify-center font-extrabold text-2xl mx-auto mb-4">
                {{ substr($user->username ?? 'C', 0, 1) }}
            </span>
            <h3 class="font-extrabold text-slate-800 text-sm">{{ $user->username }}</h3>
            <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ $user->email }}</p>

            <div class="mt-4 inline-flex items-center px-3 py-1 bg-indigo-50 border border-indigo-100 rounded-full text-xs font-bold text-indigo-700">
                ⭐ Hạng {{ $customer->membership_level ?? 'Standard' }}
            </div>

            <!-- Points progression details -->
            <div class="mt-6 border-t border-slate-100 pt-5 text-left">
                <div class="flex justify-between items-center text-xs mb-1.5">
                    <span class="text-slate-400 font-semibold">Điểm tích lũy hiện tại:</span>
                    <span class="font-bold text-amber-500">{{ number_format($customer->points ?? 0) }} Pts</span>
                </div>
                
                @php
                    $pts = $customer->points ?? 0;
                    $nextTier = 'Silver';
                    $nextPts = 100;
                    $percent = 0;

                    if ($pts >= 1000) {
                        $nextTier = 'Max Level';
                        $nextPts = 1000;
                        $percent = 100;
                    } elseif ($pts >= 500) {
                        $nextTier = 'Platinum';
                        $nextPts = 1000;
                        $percent = (($pts - 500) / 500) * 100;
                    } elseif ($pts >= 200) {
                        $nextTier = 'Diamond';
                        $nextPts = 500;
                        $percent = (($pts - 200) / 300) * 100;
                    } elseif ($pts >= 100) {
                        $nextTier = 'Gold';
                        $nextPts = 200;
                        $percent = (($pts - 100) / 100) * 100;
                    } else {
                        $nextTier = 'Silver';
                        $nextPts = 100;
                        $percent = ($pts / 100) * 100;
                    }
                    $percent = min(max($percent, 0), 100);
                @endphp

                <!-- Progress bar -->
                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden shadow-inner mt-2">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-300" style="width: {{ $percent }}%"></div>
                </div>
                <div class="flex justify-between items-center text-[10px] text-slate-450 mt-1.5 font-medium">
                    <span>Hành trình thăng hạng:</span>
                    <span>Còn {{ max($nextPts - $pts, 0) }} điểm lên hạng {{ $nextTier }}</span>
                </div>
            </div>
        </div>

        <!-- Loyalty benefit cards -->
        <div class="bg-gradient-to-tr from-slate-900 to-indigo-950 rounded-2xl p-5 text-white shadow-xl">
            <h4 class="font-extrabold text-xs uppercase tracking-wider text-indigo-400 mb-3">Quyền lợi thành viên</h4>
            <ul class="space-y-3.5 text-[11px] text-slate-300">
                <li class="flex items-start">
                    <span class="mr-2">🎁</span>
                    <span>Tích lũy 10 điểm cho mỗi đơn hàng đặt thành công trên hệ thống.</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">⚡</span>
                    <span>Hạng Bạc (Silver) trở lên nhận các voucher chiết khấu sâu hơn.</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">💎</span>
                    <span>Hạng Kim Cương & Bạch Kim được ưu tiên điều phối shipper nhanh gấp 2 lần.</span>
                </li>
            </ul>
        </div>
    </div>

    <!-- Right Profile update form -->
    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/60 shadow-sm p-5 md:p-6">
        <h4 class="font-bold text-slate-800 text-sm mb-4 pb-2 border-b border-slate-100 flex items-center">
            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
            Cập nhật thông tin tài khoản
        </h4>

        <form action="{{ route('customer.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Tên người dùng</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full rounded-xl border-slate-250/80 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition" required>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full rounded-xl border-slate-250/80 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Địa chỉ Email (Tên đăng nhập)</label>
                <input type="email" value="{{ $user->email }}" class="w-full rounded-xl border-slate-250/80 bg-slate-100 text-slate-400 px-3 py-2 text-xs font-semibold cursor-not-allowed" disabled>
                <span class="text-[9px] text-slate-400 mt-1 block">Không thể tự thay đổi địa chỉ email đăng nhập.</span>
            </div>

            <div class="border-t border-slate-100 my-4 pt-3"></div>

            <h4 class="font-bold text-slate-800 text-sm mb-4">Đổi mật khẩu (Bỏ trống nếu không thay đổi)</h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Mật khẩu mới</label>
                    <input type="password" name="password" placeholder="Tối thiểu 6 ký tự..." class="w-full rounded-xl border-slate-250/80 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Xác nhận mật khẩu mới</label>
                    <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu..." class="w-full rounded-xl border-slate-250/80 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md shadow-blue-500/10 transition">
                    Lưu các thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
