<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        // Lấy giỏ hàng từ session (ví dụ bạn lưu trong session 'cart')
        $cartItems = session('cart', []);

        // Tính toán tổng tiền
        $subtotal = collect($cartItems)->sum(fn($i) => $i['qty'] * $i['price']);
        $shipping = 0; // hoặc tính phí ship theo logic riêng
        $total = $subtotal + $shipping;

        // QR data demo - sử dụng ASCII safe characters để tránh lỗi encoding
        $fakeQrData = 'Ngan hang ABC - STK 123456789 - Thanh toan don hang - Tong tien: ' . $total . 'd';

        return view('checkout', compact('cartItems', 'subtotal', 'shipping', 'total', 'fakeQrData'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'email'          => 'required|email',
            'phone'          => 'required|string',
            'address'        => 'required|string',
            'payment_method' => 'required|in:cod,bank,card',
        ]);

        // Xử lý lưu đơn hàng vào DB, trừ tồn kho, gửi mail...

        // Xóa giỏ hàng sau khi đặt
        session()->forget('cart');

        return redirect()->route('checkout')
                         ->with('success', 'Đặt hàng thành công!')
                         ->withInput();
    }
}
