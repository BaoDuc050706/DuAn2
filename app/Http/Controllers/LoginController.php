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

            // Merge session cart vào cart của user (nếu có) — giữ cả 2 nguồn
            $sessionCart = $request->session()->get('cart', []);
            $userCart = [];
            if ($user && !empty($user->cart_json)) {
                $decoded = json_decode($user->cart_json, true);
                if (is_array($decoded)) {
                    $userCart = $decoded;
                }
            }

            // Build associative map by variant key to merge quantities
            $mergeByKey = function(array $items) {
                $map = [];
                foreach ($items as $it) {
                    $productId = isset($it['product_id']) ? (int)$it['product_id'] : 0;
                    $variantKey = $productId . '|' .
                                  (isset($it['variant_ram']) ? $it['variant_ram'] : '') . '|' .
                                  (isset($it['variant_ssd']) ? $it['variant_ssd'] : '') . '|' .
                                  (isset($it['variant_color']) ? $it['variant_color'] : '') . '|' .
                                  (isset($it['variant_switch']) ? $it['variant_switch'] : '');
                    if (!isset($map[$variantKey])) {
                        $map[$variantKey] = $it;
                    } else {
                        $map[$variantKey]['qty'] = ($map[$variantKey]['qty'] ?? 0) + ($it['qty'] ?? 0);
                    }
                }
                return $map;
            };

            $mapUser = $mergeByKey($userCart);
            $mapSession = $mergeByKey($sessionCart);

            // Merge session into user map (session has priority to add qty)
            foreach ($mapSession as $key => $item) {
                if (isset($mapUser[$key])) {
                    $mapUser[$key]['qty'] = ($mapUser[$key]['qty'] ?? 0) + ($item['qty'] ?? 0);
                } else {
                    $mapUser[$key] = $item;
                }
            }

            // Convert back to indexed array and normalize keys
            $merged = array_values($mapUser);

            // Lưu vào session và vào user->cart_json
            $request->session()->put('cart', $merged);
            if ($user) {
                $user->cart_json = json_encode($merged);
                $user->save();
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
