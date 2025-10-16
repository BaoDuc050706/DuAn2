<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        if ($request->boolean('buy_now')) {
            $name = (string) $request->query('name', 'Sản phẩm');
            $price = (int) $request->query('price', 0);
            $qty = max(1, (int) $request->query('qty', 1));
            $cartItems = [[ 'name' => $name, 'qty' => $qty, 'price' => $price ]];
        } else {
            $cartItems = [
                [ 'name' => 'Sản phẩm A', 'qty' => 1, 'price' => 1299000 ],
                [ 'name' => 'Sản phẩm B', 'qty' => 2, 'price' => 499000 ],
            ];
        }

        $subtotal = collect($cartItems)->reduce(function ($carry, $item) {
            return $carry + ($item['qty'] * $item['price']);
        }, 0);

        $shipping = $subtotal >= 2000000 ? 0 : 30000;
        $total = $subtotal + $shipping;

        return view('checkout', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    public function process(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'payment_method' => ['required', 'in:cod,bank,card'],
        ]);

        return redirect()->route('checkout.index')
            ->with('success', 'Đặt hàng thành công! Mã đơn: #' . mt_rand(100000, 999999));
    }
}


