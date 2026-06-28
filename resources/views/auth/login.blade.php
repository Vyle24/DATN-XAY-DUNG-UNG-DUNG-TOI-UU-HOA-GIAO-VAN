<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập | Tối Ưu Giao Vận</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-2xl shadow-xl border border-slate-100">
        <div>
            <div class="mx-auto h-12 w-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-200">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-slate-900">
                Tối Ưu Giao Vận
            </h2>
            <p class="mt-2 text-center text-sm text-slate-500">
                Đăng nhập hệ thống điều phối & quản lý vận đơn
            </p>
        </div>

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

        <form class="mt-8 space-y-5" action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="login_field" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email / Số điện thoại / Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206" />
                            </svg>
                        </div>
                        <input id="login_field" name="login_field" type="text" required 
                            class="appearance-none rounded-xl relative block w-full pl-10 pr-3 py-2.5 border border-slate-200 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 sm:text-sm transition-all duration-150" 
                            placeholder="Nhập email, sđt hoặc username..." value="{{ old('login_field') }}">
                    </div>
                    @error('login_field')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Mật khẩu</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input id="password" name="password" type="password" required 
                            class="appearance-none rounded-xl relative block w-full pl-10 pr-3 py-2.5 border border-slate-200 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 sm:text-sm transition-all duration-150" 
                            placeholder="••••••••">
                    </div>
                    @error('password')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember-me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded">
                    <label for="remember-me" class="ml-2 block text-xs text-slate-500 font-medium">Ghi nhớ đăng nhập</label>
                </div>
                <div class="text-xs">
                    <a href="#" class="font-semibold text-blue-600 hover:text-blue-500 transition-colors duration-155">Quên mật khẩu?</a>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2.5 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 shadow-md shadow-blue-500/10">
                    Đăng nhập hệ thống
                </button>
            </div>
        </form>

        {{-- Demo accounts block removed --}}
    </div>
</body>
</html>
