@extends('layouts.shipper')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Left Profile Card --}}
    <div class="lg:col-span-4 space-y-5">
        {{-- Avatar & Basic Info --}}
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6 text-center">
            <div class="h-20 w-20 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-extrabold text-3xl mx-auto mb-4 shadow-lg shadow-blue-500/20">
                {{ strtoupper(substr($user->username ?? 'S', 0, 1)) }}
            </div>
            <h3 class="font-extrabold text-slate-800 text-base">{{ $user->username }}</h3>
            <p class="text-[11px] text-slate-400 font-mono mt-0.5">{{ $user->email }}</p>

            <div class="mt-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                {{ $shipper->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-600 border border-rose-200' }}">
                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $shipper->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                {{ $shipper->is_active ? 'Đang Online' : 'Offline' }}
            </div>

            {{-- Stats Summary --}}
            <div class="mt-5 grid grid-cols-3 gap-3 border-t border-slate-100 pt-4">
                <div class="text-center">
                    <div class="text-lg font-extrabold text-emerald-600">{{ $totalDelivered }}</div>
                    <div class="text-[9px] text-slate-400 font-semibold uppercase mt-0.5">Đã giao</div>
                </div>
                <div class="text-center border-x border-slate-100">
                    <div class="text-lg font-extrabold text-rose-500">{{ $totalFailed }}</div>
                    <div class="text-[9px] text-slate-400 font-semibold uppercase mt-0.5">Thất bại</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-extrabold text-blue-600">{{ number_format($totalEarnings / 1000, 0) }}K</div>
                    <div class="text-[9px] text-slate-400 font-semibold uppercase mt-0.5">Thu nhập</div>
                </div>
            </div>
        </div>

        {{-- Vehicle Info Card --}}
        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-5">
            <h4 class="font-bold text-slate-700 text-xs uppercase tracking-wider mb-4 flex items-center">
                <svg class="h-4 w-4 mr-1.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg>
                Thông tin phương tiện
            </h4>
            <div class="space-y-3 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Loại xe:</span>
                    <span class="font-bold text-slate-800">{{ $shipper->vehicle_type ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Biển số xe:</span>
                    <span class="font-bold text-slate-800 font-mono">{{ $shipper->license_no ?? 'Chưa cập nhật' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Số điện thoại:</span>
                    <span class="font-bold text-slate-800">{{ $user->phone ?? 'Chưa có' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400 font-medium">Khu vực hoạt động:</span>
                    <span class="font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full text-[10px]">
                        @if($shipper->region == '10.7769,106.7009') Hồ Chí Minh
                        @elseif($shipper->region == '21.028511,105.804817') Hà Nội
                        @elseif($shipper->region == '16.0544,108.2022') Đà Nẵng
                        @elseif($shipper->region == '10.0451,105.7468') Cần Thơ
                        @else {{ $shipper->region ?? 'Chưa chọn' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Wallet Card --}}
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg shadow-blue-500/20">
            <p class="text-[10px] font-bold uppercase opacity-80 mb-1">Số dư ví hiện tại</p>
            <p class="text-3xl font-extrabold">{{ number_format($shipper->wallet_balance) }}đ</p>
            <p class="text-[10px] opacity-60 mt-1">Tích lũy từ {{ $totalDelivered }} đơn hàng thành công</p>
            <a href="{{ route('shipper.earnings') }}" class="mt-4 inline-flex items-center text-[10px] font-bold bg-white/20 hover:bg-white/30 transition px-3 py-1.5 rounded-lg">
                Xem lịch sử thu nhập →
            </a>
        </div>

        {{-- Toggle Online/Offline --}}
        <form action="{{ route('shipper.toggle-status') }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-3 rounded-2xl text-xs font-bold transition shadow-sm flex items-center justify-center space-x-2
                {{ $shipper->is_active ? 'bg-rose-50 text-rose-600 border border-rose-200 hover:bg-rose-100' : 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-md shadow-emerald-500/20' }}">
                @if($shipper->is_active)
                    <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Chuyển sang Offline</span>
                @else
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Chuyển sang Online</span>
                @endif
            </button>
        </form>

        {{-- Logout --}}
        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 text-xs font-bold text-slate-500 bg-slate-50 hover:bg-rose-50 hover:text-rose-600 rounded-xl transition shadow-sm border border-slate-200 hover:border-rose-200">
                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Đăng xuất
            </button>
        </form>
    </div>

    {{-- Right Edit Form --}}
    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/60 shadow-sm p-5 md:p-6">
        <h4 class="font-bold text-slate-800 text-sm mb-4 pb-2 border-b border-slate-100 flex items-center">
            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            Cập nhật thông tin cá nhân
        </h4>

        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-700 font-semibold">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 px-4 py-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-700">
            @foreach($errors->all() as $error)
            <div>• {{ $error }}</div>
            @endforeach
        </div>
        @endif

        <form action="{{ route('shipper.profile.update') }}" method="POST" class="space-y-5">
            @csrf

            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase mb-3">Thông tin tài khoản</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Tên đăng nhập</label>
                        <input type="text" value="{{ $user->username }}" disabled
                            class="w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-400 px-3 py-2 text-xs font-semibold cursor-not-allowed">
                        <span class="text-[9px] text-slate-400 mt-1 block">Tên đăng nhập không thể thay đổi.</span>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Email</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-400 px-3 py-2 text-xs font-semibold cursor-not-allowed">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        placeholder="09xxxxxxxx" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Khu vực hoạt động</label>
                    <select name="region" class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition cursor-pointer">
                        <option value="10.7769,106.7009" {{ $shipper->region == '10.7769,106.7009' ? 'selected' : '' }}>TP. Hồ Chí Minh</option>
                        <option value="21.028511,105.804817" {{ $shipper->region == '21.028511,105.804817' ? 'selected' : '' }}>Thủ đô Hà Nội</option>
                        <option value="16.0544,108.2022" {{ $shipper->region == '16.0544,108.2022' ? 'selected' : '' }}>TP. Đà Nẵng</option>
                        <option value="10.0451,105.7468" {{ $shipper->region == '10.0451,105.7468' ? 'selected' : '' }}>TP. Cần Thơ</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Loại phương tiện</label>
                    <input type="text" value="{{ ($shipper->vehicle_type === 'Motorbike' || $shipper->vehicle_type === 'Xe may') ? 'Xe máy' : (($shipper->vehicle_type === 'Electric' || $shipper->vehicle_type === 'Xe dien') ? 'Xe điện' : (($shipper->vehicle_type === 'Van' || $shipper->vehicle_type === 'Xe tai nho') ? 'Xe tải nhỏ' : ($shipper->vehicle_type ?? 'Chưa xác định'))) }}" disabled
                        class="w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-500 px-3 py-2 text-xs font-semibold cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Biển số xe</label>
                    <input type="text" value="{{ $shipper->license_no ?? 'Chưa có' }}" disabled
                        class="w-full rounded-xl border border-slate-200 bg-slate-100 text-slate-500 px-3 py-2 text-xs font-semibold cursor-not-allowed font-mono">
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">* Biển số xe & Loại phương tiện do Ban quản trị thiết lập và quản lý. Vui lòng liên hệ Admin để cập nhật thông tin này.</p>

            <div class="border-t border-slate-100 pt-4">
                <h4 class="font-bold text-slate-800 text-sm mb-4">Đổi mật khẩu (Bỏ trống nếu không thay đổi)</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Mật khẩu mới</label>
                        <input type="password" name="password" placeholder="Tối thiểu 6 ký tự..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Xác nhận mật khẩu</label>
                        <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu..."
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-semibold focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md shadow-blue-500/10 transition">
                    Lưu các thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
