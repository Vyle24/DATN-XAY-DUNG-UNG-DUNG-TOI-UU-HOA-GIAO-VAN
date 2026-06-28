<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->role_id == 2) {
                return redirect()->route('shipper.dashboard');
            } elseif (Auth::user()->role_id == 3) {
                return redirect()->route('customer.dashboard');
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_field' => 'required',
            'password' => 'required',
        ]);

        $loginField = $request->input('login_field');
        $password = $request->input('password');

        $fieldType = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        // Check if it's a phone number (all digits)
        if (preg_match('/^[0-9]+$/', $loginField)) {
            $fieldType = 'phone';
        }

        if (Auth::attempt([$fieldType => $loginField, 'password' => $password], $request->has('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role_id == 1) {
                return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập Admin thành công!');
            } elseif (Auth::user()->role_id == 2) {
                return redirect()->route('shipper.dashboard')->with('success', 'Đăng nhập Shipper thành công!');
            } elseif (Auth::user()->role_id == 3) {
                return redirect()->route('customer.dashboard')->with('success', 'Đăng nhập Khách hàng thành công!');
            }

            return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'login_field' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('login_field');
    }

    public function quickLogin($userId)
    {
        $user = User::find($userId);
        if ($user) {
            Auth::login($user);
            session()->regenerate();

            if ($user->role_id == 1) {
                return redirect()->route('admin.dashboard')->with('success', "Đăng nhập nhanh với vai trò {$user->username}!");
            } elseif ($user->role_id == 2) {
                return redirect()->route('shipper.dashboard')->with('success', "Đăng nhập nhanh với vai trò {$user->username}!");
            } elseif ($user->role_id == 3) {
                return redirect()->route('customer.dashboard')->with('success', "Đăng nhập nhanh với vai trò {$user->username}!");
            }
        }

        return redirect()->route('login')->with('error', 'Không tìm thấy người dùng này.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Đã đăng xuất.');
    }
}
