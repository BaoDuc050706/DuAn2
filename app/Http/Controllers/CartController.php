<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    // Hiển thị giỏ hàng
    public function index(Request $request): View
    {
        $cartItems = $request->session()->get('cart', []);
        $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['qty']);

        return view('cart.index', compact('cartItems', 'total'));
    }

    // Thêm sản phẩm vào giỏ
    public function add(Request $request): RedirectResponse
    {
        $productId = $request->input('product_id');
        $cart = $request->session()->get('cart', []);

        // Kiểm tra xem sản phẩm đã có trong giỏ chưa
        $existingIndex = collect($cart)->search(fn($item) => $item['product_id'] == $productId);

        if ($existingIndex !== false) {
            // Nếu có rồi thì tăng số lượng
            $cart[$existingIndex]['qty'] += $request->input('qty', 1);
        } else {
            // Nếu chưa có thì thêm mới
            $cart[] = [
                'product_id' => $productId,
                'name' => $request->input('name'),
                'price' => $request->input('price'),
                'qty' => $request->input('qty', 1),
                'slug' => $request->input('slug'),
            ];
        }

        $request->session()->put('cart', $cart);
        return redirect()->route('cart.index')->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    // Tăng số lượng
    public function increment(Request $request, $index): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (isset($cart[$index])) {
            $cart[$index]['qty']++;
            $request->session()->put('cart', $cart);
        }
        return redirect()->route('cart.index');
    }

    // Giảm số lượng
    public function decrement(Request $request, $index): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (isset($cart[$index]) && $cart[$index]['qty'] > 1) {
            $cart[$index]['qty']--;
            $request->session()->put('cart', $cart);
        }
        return redirect()->route('cart.index');
    }

    // Xóa 1 sản phẩm khỏi giỏ
    public function remove(Request $request, $index): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            $request->session()->put('cart', array_values($cart));
        }
        return redirect()->route('cart.index');
    }

    // Xóa toàn bộ giỏ hàng
    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');
        return redirect()->route('cart.index')->with('success', 'Đã xóa giỏ hàng.');
    }
}
