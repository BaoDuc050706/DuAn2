<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $request->session()->get('cart');
        if ($cart === null) {
            $cart = [
                [ 'name' => 'Sản phẩm A', 'qty' => 1, 'price' => 1299000 ],
                [ 'name' => 'Sản phẩm B', 'qty' => 2, 'price' => 499000 ],
            ];
            $request->session()->put('cart', $cart);
        }

        return view('cart', ['cartItems' => $cart]);
    }

    public function remove(Request $request, int $index): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (array_key_exists($index, $cart)) {
            array_splice($cart, $index, 1);
            $request->session()->put('cart', $cart);
            if ($request->user()) {
                $request->user()->fill(['cart_json' => json_encode($cart)])->save();
            }
        }
        return redirect()->route('cart.index')->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function increment(Request $request, int $index): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (array_key_exists($index, $cart)) {
            $cart[$index]['qty'] = max(1, (int)($cart[$index]['qty'] ?? 1) + 1);
            $request->session()->put('cart', $cart);
            if ($request->user()) {
                $request->user()->fill(['cart_json' => json_encode($cart)])->save();
            }
        }
        return redirect()->route('cart.index');
    }

    public function decrement(Request $request, int $index): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (array_key_exists($index, $cart)) {
            $newQty = max(1, (int)($cart[$index]['qty'] ?? 1) - 1);
            $cart[$index]['qty'] = $newQty;
            $request->session()->put('cart', $cart);
            if ($request->user()) {
                $request->user()->fill(['cart_json' => json_encode($cart)])->save();
            }
        }
        return redirect()->route('cart.index');
    }

    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'qty' => ['required', 'integer', 'min:1'],
            'slug' => ['nullable', 'string', 'max:255'],
        ]);

        $cart = $request->session()->get('cart', []);

        $foundIndex = null;
        foreach ($cart as $idx => $item) {
            // Prefer matching by slug if provided
            if (!empty($validated['slug']) && ($item['slug'] ?? null) === $validated['slug']) {
                $foundIndex = $idx;
                break;
            }
            if (($item['name'] ?? null) === trim($validated['name']) && (int)($item['price'] ?? 0) === (int)$validated['price']) {
                $foundIndex = $idx;
                break;
            }
        }

        if ($foundIndex !== null) {
            $cart[$foundIndex]['qty'] = (int)($cart[$foundIndex]['qty'] ?? 1) + (int)$validated['qty'];
        } else {
            $cart[] = [
                'name' => $validated['name'],
                'price' => (int)$validated['price'],
                'qty' => (int)$validated['qty'],
                'slug' => $validated['slug'] ?? null,
            ];
        }

        $request->session()->put('cart', $cart);

        // Persist cart to user if logged in
        if ($request->user()) {
            $request->user()->fill(['cart_json' => json_encode($cart)])->save();
        }

        return back()->with('success', 'Đã thêm vào giỏ hàng.');
    }
}


