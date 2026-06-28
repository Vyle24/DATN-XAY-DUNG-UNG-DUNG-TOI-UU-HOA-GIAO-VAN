<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipper Portal | Tối Ưu Giao Vận</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Leaflet JS & CSS for Interactive Maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Glassmorphism utility classes */
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        /* Leaflet Z-Index Reset */
        .leaflet-container {
            z-index: 1 !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- DESKTOP LEFT SIDEBAR -->
    <aside class="hidden md:flex flex-col w-64 bg-slate-900 text-slate-100 min-h-screen fixed top-0 left-0 z-30 shadow-xl border-r border-slate-800">
        <!-- Logo / Title -->
        <div class="h-20 flex items-center px-6 bg-slate-955/40 border-b border-slate-800/80">
            <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center mr-3 shadow-lg shadow-blue-500/30">
                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <h1 class="text-sm font-bold tracking-tight text-white uppercase">Shipper Portal</h1>
                <p class="text-[10px] text-slate-400 font-medium">Hệ thống điều phối</p>
            </div>
        </div>

        <!-- Profile / Status Quick Card -->
        <div class="p-5 border-b border-slate-800 bg-slate-955/20">
            <div class="flex items-center space-x-3 mb-4">
                <span class="h-10 w-10 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 flex items-center justify-center font-extrabold text-base flex-shrink-0">
                    {{ substr($shipper->user->username ?? 'S', 0, 1) }}
                </span>
                <div class="overflow-hidden">
                    <h4 class="text-xs font-bold text-slate-200 truncate">{{ $shipper->user->username ?? 'N/A' }}</h4>
                    <p class="text-[10px] text-slate-500 truncate font-mono">{{ $shipper->license_no ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Wallet & Status -->
            <div class="bg-slate-800/50 rounded-xl p-3 space-y-2.5">
                <div class="flex justify-between items-center text-[11px]">
                    <span class="text-slate-400">Số dư ví:</span>
                    <span class="font-bold text-emerald-400">{{ number_format($shipper->wallet_balance ?? 0) }}đ</span>
                </div>
                <div class="flex justify-between items-center text-[11px]">
                    <span class="text-slate-400">Phương tiện:</span>
                    <span class="font-semibold text-blue-400">{{ $shipper->vehicle_type ?? 'N/A' }}</span>
                </div>
                <!-- Status Toggle -->
                <form action="{{ route('shipper.toggle-status') }}" method="POST" class="pt-1.5 border-t border-slate-700/50">
                    @csrf
                    <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-1.5 rounded-lg text-[10px] font-bold transition {{ $shipper->is_active ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/20 hover:bg-rose-500/15 hover:text-rose-400 hover:border-rose-500/20' : 'bg-slate-700/50 text-slate-400 border border-slate-600 hover:bg-emerald-500/15 hover:text-emerald-400 hover:border-emerald-500/20' }}">
                        <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $shipper->is_active ? 'bg-emerald-400 animate-pulse' : 'bg-slate-500' }}"></span>
                        {{ $shipper->is_active ? 'Trực tuyến (Online)' : 'Ngoại tuyến (Offline)' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-grow p-4 space-y-1">
            @php
                $activeTab = request('tab', 'dashboard');
                if (request()->has('page')) $activeTab = 'history'; // if pagination, stay on history
            @endphp
            
            <a href="{{ route('shipper.dashboard', ['tab' => 'dashboard']) }}" class="w-full flex items-center px-4 py-3 text-xs font-bold rounded-xl transition duration-150 {{ request()->routeIs('shipper.dashboard') && $activeTab === 'dashboard' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Bảng điều khiển
            </a>
            <a href="{{ route('shipper.dashboard', ['tab' => 'route']) }}" class="w-full flex items-center px-4 py-3 text-xs font-bold rounded-xl transition duration-150 {{ request()->routeIs('shipper.dashboard') && $activeTab === 'route' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" />
                </svg>
                Hành trình tối ưu (Map)
            </a>
            <a href="{{ route('shipper.dashboard', ['tab' => 'history']) }}" class="w-full flex items-center px-4 py-3 text-xs font-bold rounded-xl transition duration-150 {{ request()->routeIs('shipper.dashboard') && $activeTab === 'history' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                Lịch sử giao hàng
            </a>
            
            <a href="{{ route('shipper.profile') }}" class="w-full flex items-center px-4 py-3 text-xs font-bold rounded-xl transition duration-150 {{ request()->routeIs('shipper.profile') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Thông tin cá nhân
            </a>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-slate-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-2.5 text-xs font-bold text-slate-400 bg-slate-800/80 hover:bg-rose-955 hover:text-rose-400 rounded-xl transition duration-150 shadow-sm border border-slate-700/30">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    <!-- MOBILE TOP STICKY HEADER -->
    <header class="flex md:hidden sticky top-0 z-20 bg-blue-600 text-white px-4 py-3.5 items-center justify-between shadow-md">
        <div class="flex items-center space-x-2.5">
            <div class="h-8 w-8 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 01-4 0m12 0a2 2 0 11-4 0 2 2 0 01-4 0m9-1v-4a2 2 0 00-2-2h-3V8a1 1 0 00-1-1H4a1 1 0 00-1 1v8h12m4 0h2a2 2 0 002-2v-3a2 2 0 00-2-2h-3v7" /></svg>
            </div>
            <div>
                <h1 class="text-xs font-bold tracking-tight text-white uppercase">{{ $shipper->user->username ?? 'Shipper' }}</h1>
                <p class="text-[9px] text-blue-200 font-mono">{{ $shipper->license_no }}</p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <!-- Notifications Bell (Mobile) -->
            <div class="relative group">
                <button class="relative p-1.5 text-white/80 hover:text-white transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                        <span class="absolute top-1 right-1 h-2 w-2 bg-rose-500 rounded-full border border-blue-600"></span>
                    @endif
                </button>
                <!-- Dropdown (Mobile) -->
                <div class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 text-slate-800">
                    <div class="p-3 border-b border-slate-50 flex justify-between items-center">
                        <span class="text-xs font-bold">Thông báo</span>
                        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                            <a href="{{ route('notifications.markAllRead') }}" class="text-[10px] text-blue-600 hover:underline">Đã đọc</a>
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

            <!-- Active Status badge & click action to toggle status -->
            <form action="{{ route('shipper.toggle-status') }}" method="POST">
                @csrf
                <button type="submit" class="inline-flex items-center px-2 py-1 rounded-full text-[9px] font-bold transition shadow-sm border {{ $shipper->is_active ? 'bg-emerald-500 text-white border-emerald-400' : 'bg-slate-700 text-slate-300 border-slate-600' }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $shipper->is_active ? 'bg-white animate-ping' : 'bg-slate-400' }}"></span>
                    {{ $shipper->is_active ? 'Online' : 'Offline' }}
                </button>
            </form>
        </div>
    </header>

    <!-- MAIN WRAPPER (Padding for desktop sidebar and mobile bottom nav) -->
    <div class="flex-grow flex flex-col md:pl-64 pb-20 md:pb-6">
        
        <!-- TOP VIEW TITLE BAR (Desktop only) -->
        <div class="hidden md:flex items-center justify-between h-20 px-8 bg-white border-b border-slate-200">
            <div>
                <h2 class="text-base font-bold text-slate-800" id="desktop-view-title">
                    @if(request()->routeIs('shipper.dashboard') && $activeTab === 'route')
                        Hành trình tối ưu (Map)
                    @elseif(request()->routeIs('shipper.dashboard') && $activeTab === 'history')
                        Lịch sử giao nhận
                    @elseif(request()->routeIs('shipper.profile'))
                        Thông tin cá nhân
                    @else
                        Bảng điều khiển giao vận
                    @endif
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Giám sát and hoàn thành lịch trình đơn hàng của bạn</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Notifications Bell (Desktop) -->
                <div class="relative group">
                    <button class="relative p-2 text-slate-400 hover:text-blue-600 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        @if(Auth::check() && Auth::user()->unreadNotifications->count() > 0)
                            <span class="absolute top-1.5 right-1.5 h-2 w-2 bg-rose-500 rounded-full border border-white"></span>
                        @endif
                    </button>
                    <!-- Dropdown (Desktop) -->
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

                <div class="h-8 w-px bg-slate-200"></div>

                <div class="text-right">
                    <span class="text-[10px] text-slate-400 block font-semibold uppercase">Ví tài khoản</span>
                    <span class="text-sm font-extrabold text-slate-800">{{ number_format($shipper->wallet_balance ?? 0) }}đ</span>
                </div>
            </div>
        </div>

        <!-- MAIN SCROLLABLE CONTENT -->
        <main class="flex-grow p-4 md:p-8">
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

    <!-- MOBILE FLOATING BOTTOM TAB BAR -->
    <nav class="flex md:hidden fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-md border-t border-slate-200/80 h-16 items-center justify-around z-20 px-4 shadow-lg">
        @php
            $activeTab = request('tab', 'dashboard');
            if (request()->has('page')) $activeTab = 'history'; // if pagination, stay on history
        @endphp
        <a href="{{ route('shipper.dashboard', ['tab' => 'dashboard']) }}" class="flex flex-col items-center justify-center space-y-1 active:scale-95 transition {{ request()->routeIs('shipper.dashboard') && $activeTab === 'dashboard' ? 'text-blue-600' : 'text-slate-400' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span class="text-[9px] font-bold">Trang chủ</span>
        </a>

        <a href="{{ route('shipper.dashboard', ['tab' => 'route']) }}" class="flex flex-col items-center justify-center space-y-1 active:scale-95 transition {{ request()->routeIs('shipper.dashboard') && $activeTab === 'route' ? 'text-blue-600' : 'text-slate-400' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L16 4m0 13V4m0 0L9 7" />
            </svg>
            <span class="text-[9px] font-bold">Hành trình</span>
        </a>

        <a href="{{ route('shipper.dashboard', ['tab' => 'history']) }}" class="flex flex-col items-center justify-center space-y-1 active:scale-95 transition {{ request()->routeIs('shipper.dashboard') && $activeTab === 'history' ? 'text-blue-600' : 'text-slate-400' }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <span class="text-[9px] font-bold">Lịch sử</span>
        </a>

        <a href="{{ route('shipper.profile') }}" class="flex flex-col items-center justify-center space-y-1 active:scale-95 transition {{ request()->routeIs('shipper.profile') ? 'text-blue-600 font-bold' : 'text-slate-400' }}" id="mobile-tab-profile">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span class="text-[9px] font-bold">Cá nhân</span>
        </a>
    </nav>

    <!-- Script to handle responsive tabs removed, relying on server-side rendering -->
    <script>
        // Initialization if needed
    </script>
</body>
</html>
