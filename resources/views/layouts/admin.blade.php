<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Tối Ưu Giao Vận</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Leaflet Map CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
        /* Leaflet Z-Index adjustments to prevent overlaying headers/modals */
        .leaflet-container {
            z-index: 1 !important;
        }
        .leaflet-pane {
            z-index: 1 !important;
        }
        .leaflet-top, .leaflet-bottom {
            z-index: 2 !important;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex text-slate-800">
    <!-- Sidebar -->
    <aside class="w-72 bg-slate-900 text-white flex flex-col justify-between hidden lg:flex flex-shrink-0 border-r border-slate-800 shadow-xl">
        <div class="overflow-y-auto">
            <!-- Logo / Brand Header -->
            <div class="h-20 flex items-center px-8 bg-slate-955/60 border-b border-slate-800/80 sticky top-0 z-20">
                <div class="h-10 w-10 rounded-xl bg-blue-600 flex items-center justify-center mr-3.5 shadow-lg shadow-blue-500/20">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <div>
                    <span class="text-base font-extrabold tracking-wide text-white block">Logistics Admin</span>
                    <span class="text-[10px] text-blue-400 font-semibold tracking-wider uppercase block">Optimized Routes</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 px-5 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                    Tổng quan thống kê
                </a>

                <div class="text-[10px] font-bold text-slate-500 uppercase px-4 pt-4 pb-1 tracking-widest">Giao Vận</div>

                <a href="{{ route('admin.orders') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.orders') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Quản lý đơn hàng
                </a>

                <a href="{{ route('admin.routes') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.routes') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    Quản lý tuyến đường
                </a>

                <a href="{{ route('admin.dispatch') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.dispatch') ? 'bg-violet-600 text-white shadow-lg shadow-violet-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Điều Phối Thông Minh
                </a>


                <a href="{{ route('admin.hubs') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.hubs') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Quản lý Hub (Trạm)
                </a>

                <div class="text-[10px] font-bold text-slate-500 uppercase px-4 pt-4 pb-1 tracking-widest">Thành Viên</div>

                <a href="{{ route('admin.users') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.users') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Quản lý Người Dùng
                    <span class="ml-auto text-[9px] font-bold bg-slate-700 px-1.5 py-0.5 rounded text-slate-300">Tất cả</span>
                </a>

                <a href="{{ route('admin.shippers') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.shippers') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Quản lý Shipper
                </a>

                <a href="{{ route('admin.customers') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.customers') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Quản lý Khách Hàng
                </a>

                <div class="text-[10px] font-bold text-slate-500 uppercase px-4 pt-4 pb-1 tracking-widest">Cấu Hình Cước</div>

                <a href="{{ route('admin.rates') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.rates') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    Biểu Phí Giao Hàng
                </a>

                <a href="{{ route('admin.vouchers') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.vouchers') ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/15 font-semibold' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 transition group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Khuyến Mãi (Vouchers)
                </a>
            </nav>
        </div>

        <!-- Logout & User Profile Card -->
        <div class="p-5 border-t border-slate-800 bg-slate-950/40">
            <div class="flex items-center mb-5 bg-slate-900/50 p-3 rounded-xl border border-slate-800/50">
                <div class="h-10 w-10 rounded-full bg-blue-500/15 border border-blue-500/30 flex items-center justify-center font-extrabold text-sm text-blue-400 mr-3">
                    AD
                </div>
                <div class="overflow-hidden">
                    <h4 class="text-sm font-semibold text-slate-200 truncate">{{ Auth::user()->username }}</h4>
                    <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center px-4 py-3 text-sm font-semibold text-slate-400 bg-slate-800/80 hover:bg-rose-950 hover:text-rose-400 rounded-xl transition duration-150 shadow-sm">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="flex-grow flex flex-col min-h-screen">
        <!-- Header -->
        <header class="h-20 bg-white border-b border-slate-200/60 flex items-center justify-between px-8 sticky top-0 z-10 shadow-sm">
            <!-- Mobile Brand Header -->
            <div class="flex items-center lg:hidden">
                <div class="h-9 w-9 rounded-lg bg-blue-600 flex items-center justify-center mr-3 shadow-md shadow-blue-500/20">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-base font-extrabold text-slate-800">Logistics Admin</span>
            </div>

            <!-- Desktop Page Title -->
            <div class="hidden lg:block">
                <h2 class="text-xl font-bold text-slate-850">
                    @yield('header_title', 'Hệ Thống Quản Trị')
                </h2>
            </div>

            <!-- Status Indicator & Mobile Menu Toggle -->
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-2 animate-pulse"></span>
                    Live Data Active
                </span>
                
                <!-- Mobile Navigation Menu Toggle (Quick Menu) -->
                <div class="relative lg:hidden group">
                    <button class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition duration-150">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <!-- Mobile Menu Dropdown -->
                    <div class="absolute right-0 mt-2 w-56 bg-slate-900 text-white rounded-2xl shadow-xl py-2 hidden group-hover:block border border-slate-850 z-30">
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Tổng quan</a>
                        <a href="{{ route('admin.orders') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Đơn hàng</a>
                        <a href="{{ route('admin.routes') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Tuyến đường</a>
                        <a href="{{ route('admin.dispatch') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Điều phối</a>
                        <a href="{{ route('admin.hubs') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Hubs</a>
                        <a href="{{ route('admin.users') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Người Dùng</a>
                        <a href="{{ route('admin.shippers') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Shippers</a>
                        <a href="{{ route('admin.customers') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Khách Hàng</a>
                        <a href="{{ route('admin.rates') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Biểu Phí Giao</a>
                        <a href="{{ route('admin.vouchers') }}" class="block px-4 py-2 text-xs font-semibold hover:bg-slate-800">Khuyến Mãi</a>
                        <div class="border-t border-slate-800 my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-xs font-semibold text-rose-400 hover:bg-slate-800">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-grow p-6 lg:p-10">
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
</body>
</html>
