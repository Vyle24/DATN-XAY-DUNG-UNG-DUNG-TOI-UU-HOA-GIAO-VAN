<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  int  $roleId
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $roleId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập.');
        }

        if (Auth::user()->role_id != $roleId) {
            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            } elseif (Auth::user()->role_id == 2) {
                return redirect()->route('shipper.dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            } elseif (Auth::user()->role_id == 3) {
                return redirect()->route('customer.dashboard')->with('error', 'Bạn không có quyền truy cập trang này.');
            }
            return redirect()->route('login')->with('error', 'Tài khoản của bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
