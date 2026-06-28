<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Portal | Tối Ưu Giao Vận</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Leaflet Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .leaflet-container {
            z-index: 1 !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col md:flex-row">

    <!-- DESKTOP LEFT SIDEBAR -->
    <aside class="hidden md:flex flex-col w-72 bg-slate-900 text-slate-100 min-h-screen fixed top-0 left-0 z-30 shadow-xl border-r border-slate-800">
        <!-- Logo / Brand Header -->
        <div class="h-20 flex items-center px-6 bg-slate-955/40 border-b border-slate-800/80">
            <div class="h-10 w-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center mr-3 shadow-lg shadow-blue-500/25">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
            <div>
                <h1 class="text-sm font-bold tracking-tight text-white uppercase">Customer Portal</h1>
                <p class="text-[10px] text-slate-400 font-medium">Hệ thống đặt đơn & theo dõi</p>
            </div>
        </div>

        <!-- Profile Quick Info -->
        <div class="p-5 border-b border-slate-800 bg-slate-955/10">
            <div class="flex items-center space-x-3 mb-4">
                <span class="h-10 w-10 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-extrabold text-base flex-shrink-0">
                    {{ substr($customer->user->username ?? 'C', 0, 1) }}
                </span>
                <div class="overflow-hidden">
                    <h4 class="text-xs font-bold text-slate-200 truncate">{{ $customer->user->username ?? 'N/A' }}</h4>
                    <p class="text-[10px] text-slate-500 truncate font-mono">{{ $customer->user->email ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Customer membership level & points -->
            <div class="bg-slate-800/50 rounded-xl p-3 space-y-2 text-[11px]">
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Hạng khách hàng:</span>
                    <span class="font-extrabold text-indigo-400">{{ $customer->membership_level ?? 'Standard' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Điểm tích lũy:</span>
                    <span class="font-bold text-amber-400">{{ number_format($customer->points ?? 0) }} Pts</span>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow p-4 space-y-1">
            <a href="{{ route('customer.dashboard') }}" class="flex items-center px-4 py-3 text-xs font-bold rounded-xl transition-all duration-150 {{ request()->routeIs('customer.dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/10' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Trang tổng quan
            </a>
            <a href="{{ route('customer.orders.create') }}" class="flex items-center px-4 py-3 text-xs font-bold rounded-xl transition-all duration-150 {{ request()->routeIs('customer.orders.create') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/10' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tạo đơn hàng mới
            </a>
            <a href="{{ route('customer.vouchers') }}" class="flex items-center px-4 py-3 text-xs font-bold rounded-xl transition-all duration-150 {{ request()->routeIs('customer.vouchers') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/10' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
                Mã giảm giá
            </a>
            <a href="{{ route('customer.profile') }}" class="flex items-center px-4 py-3 text-xs font-bold rounded-xl transition-all duration-150 {{ request()->routeIs('customer.profile') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/10' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Tài khoản cá nhân
            </a>
        </nav>

        <!-- Logout Action -->
        <div class="p-4 border-t border-slate-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 text-xs font-bold rounded-xl text-rose-400 hover:bg-rose-500/10 transition duration-150">
                    <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    <!-- CONTENT WRAPPER -->
    <div class="flex-1 flex flex-col md:pl-72 min-h-screen pb-20 md:pb-0">
        <!-- TOP NAVBAR -->
        <header class="h-16 bg-white border-b border-slate-200/80 sticky top-0 z-20 px-6 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-3">
                <!-- Mobile Logo Icon -->
                <div class="h-8 w-8 rounded-lg bg-blue-600 flex md:hidden items-center justify-center shadow-md shadow-blue-500/20">
                    <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <h2 class="text-sm md:text-base font-bold text-slate-800">@yield('header_title', 'Trang Khách Hàng')</h2>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Notifications Bell -->
                <div class="relative group">
                    <button class="relative p-2 text-slate-400 hover:text-blue-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1.5 right-1.5 h-2 w-2 bg-rose-500 rounded-full border border-white"></span>
                        @endif
                    </button>
                    <!-- Dropdown -->
                    <div class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="p-3 border-b border-slate-50 flex justify-between items-center">
                            <span class="text-xs font-bold text-slate-800">Thông báo</span>
                            @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                                <a href="{{ route('notifications.markAllRead') }}" class="text-[10px] text-blue-600 hover:underline">Đánh dấu đã đọc</a>
                            @endif
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                                @foreach(Auth::user()->unreadNotifications->take(5) as $notification)
                                    <div class="p-3 border-b border-slate-50 hover:bg-slate-50/50 transition">
                                        <div class="text-[10px] font-bold text-slate-800">{{ $notification->data['title'] ?? 'Thông báo' }}</div>
                                        <div class="text-[10px] text-slate-500 mt-0.5">{{ $notification->data['message'] ?? '' }}</div>
                                        <div class="text-[9px] text-slate-400 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                @endforeach
                            @else
                                <div class="p-4 text-center text-[10px] text-slate-400">Không có thông báo mới</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Points badge -->
                <div class="hidden sm:flex items-center bg-amber-50 border border-amber-100 rounded-full px-3 py-1 text-[11px] font-bold text-amber-700">
                    <span class="mr-1">⭐</span>
                    <span>{{ number_format($customer->points ?? 0) }} Pts</span>
                </div>
                
                <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>
                
                <!-- Quick User Name Dropdown / Display -->
                <div class="flex items-center space-x-2.5">
                    <div class="text-right hidden sm:block">
                        <span class="text-xs font-bold text-slate-800 block">{{ $customer->user->username ?? 'N/A' }}</span>
                        <span class="text-[9px] font-semibold text-slate-400 block tracking-wider uppercase">{{ $customer->membership_level ?? 'Standard' }} Member</span>
                    </div>
                    <span class="h-9 w-9 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 flex items-center justify-center font-extrabold text-sm">
                        {{ substr($customer->user->username ?? 'C', 0, 1) }}
                    </span>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-grow p-4 md:p-6 lg:p-8">
            <!-- TOAST Notifications -->
            <div id="toast-container" class="fixed top-5 right-5 z-[100] flex flex-col space-y-3 pointer-events-none">
                @if(session('success'))
                    <div class="toast-message pointer-events-auto w-80 bg-white/95 backdrop-blur-md border border-emerald-200 shadow-xl rounded-2xl p-4 flex items-start transition-all transform duration-300">
                        <div class="flex-shrink-0 bg-emerald-100 text-emerald-600 p-2 rounded-xl">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-bold text-slate-800">Thành công!</h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">{{ session('success') }}</p>
                        </div>
                        <button onclick="this.closest('.toast-message').remove()" class="ml-2 text-slate-400 hover:text-slate-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="toast-message pointer-events-auto w-80 bg-white/95 backdrop-blur-md border border-rose-200 shadow-xl rounded-2xl p-4 flex items-start transition-all transform duration-300">
                        <div class="flex-shrink-0 bg-rose-100 text-rose-600 p-2 rounded-xl">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-sm font-bold text-slate-800">Có lỗi xảy ra</h3>
                            <p class="text-[11px] font-medium text-slate-500 mt-0.5">{{ session('error') }}</p>
                        </div>
                        <button onclick="this.closest('.toast-message').remove()" class="ml-2 text-slate-400 hover:text-slate-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                @endif
            </div>

            <script>
                // Auto-hide toast after 4 seconds
                setTimeout(() => {
                    document.querySelectorAll('.toast-message').forEach(toast => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(-10px)';
                        setTimeout(() => toast.remove(), 300);
                    });
                }, 4000);
            </script>

            @yield('content')
        </main>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION BAR -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-white/95 backdrop-blur-md border-t border-slate-200 flex items-center justify-around z-30 shadow-[0_-2px_10px_rgba(0,0,0,0.05)]">
        <a href="{{ route('customer.dashboard') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('customer.dashboard') ? 'text-blue-600 font-bold' : 'text-slate-400' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[9px] mt-1">Tổng quan</span>
        </a>
        <a href="{{ route('customer.orders.create') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('customer.orders.create') ? 'text-blue-600 font-bold' : 'text-slate-400' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-[9px] mt-1">Đặt đơn</span>
        </a>
        <a href="{{ route('customer.vouchers') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('customer.vouchers') ? 'text-blue-600 font-bold' : 'text-slate-400' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
            <span class="text-[9px] mt-1">Khuyến mãi</span>
        </a>
        <a href="{{ route('customer.profile') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('customer.profile') ? 'text-blue-600 font-bold' : 'text-slate-400' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-[9px] mt-1">Cá nhân</span>
        </a>
    </nav>

    @yield('scripts')
</body>
</html>
