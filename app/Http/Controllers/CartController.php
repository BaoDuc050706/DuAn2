<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    /**
     * Hiển thị giỏ hàng
     */
    public function index(Request $request): View
    {
        $cartItems = $request->session()->get('cart', []);
        $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['qty']);

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request): RedirectResponse
    {
        $productId = (int) $request->input('product_id');
        $cart = $request->session()->get('cart', []);

        // ✅ Chỉ kiểm tra theo product_id
        $existingIndex = collect($cart)->search(fn($item) =>
            (int) $item['product_id'] === $productId
        );

        if ($existingIndex !== false) {
            // ✅ Nếu đã có -> tăng số lượng
            $cart[$existingIndex]['qty'] += (int) $request->input('qty', 1);
        } else {
            // ✅ Nếu chưa có -> thêm mới
            $cart[] = [
                'product_id' => $productId,
                'name'       => $request->input('name'),
                'price'      => (int) $request->input('price'),
                'qty'        => (int) $request->input('qty', 1),
                'slug'       => $request->input('slug'),
            ];
        }

        // ✅ RẤT QUAN TRỌNG: reset key để tránh tách sản phẩm
        $cart = array_values($cart);

        // ✅ Lưu lại giỏ hàng
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')
                         ->with('success', 'Đã thêm sản phẩm vào giỏ hàng!');
    }

    /**
     * Tăng số lượng
     */
    public function increment(Request $request, int $index): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (isset($cart[$index])) {
            $cart[$index]['qty']++;
            $request->session()->put('cart', $cart);
        }
        return redirect()->route('cart.index');
    }

    /**
     * Giảm số lượng
     */
    public function decrement(Request $request, int $index): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (isset($cart[$index]) && $cart[$index]['qty'] > 1) {
            $cart[$index]['qty']--;
            $request->session()->put('cart', $cart);
        }
        return redirect()->route('cart.index');
    }

    /**
     * Xóa sản phẩm
     */
    public function remove(Request $request, int $index): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if (isset($cart[$index])) {
            unset($cart[$index]);
            // ✅ reset key
            $cart = array_values($cart);
            $request->session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')
                         ->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng!');
    }

    /**
     * Xóa toàn bộ
     */
    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart');
        return redirect()->route('cart.index')
                         ->with('success', 'Đã xóa toàn bộ giỏ hàng!');
    }
}
