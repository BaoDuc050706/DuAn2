<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function submit(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        $remember = (bool) $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Restore cart nếu có
            if ($user && !empty($user->cart_json)) {
                $decoded = json_decode($user->cart_json, true);
                if (is_array($decoded)) {
                    $request->session()->put('cart', $decoded);
                }
            }

            // Admin chuyển vào dashboard, user thường về home
            $target = ($user && ($user->role === 'admin')) ? route('admin.dashboard') : route('home');

            return redirect()->intended($target)->with('success', 'Đăng nhập thành công.');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
