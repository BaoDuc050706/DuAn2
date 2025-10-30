<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
	public function index(Request $request): View|RedirectResponse
	{
		// Enforce max 20 units per product
		if ($request->boolean('buy_now')) {
			$name = (string) $request->query('name', 'Sản phẩm');
			$price = (int) $request->query('price', 0);
			$qty = (int) $request->query('qty', 1);
			if ($qty < 1) {
				$qty = 1;
			}
			if ($qty > 20) {
				$qty = 20;
			}
			$cartItems = [['name' => $name, 'qty' => $qty, 'price' => $price]];
			// persist to session cart
			$request->session()->put('cart', $cartItems);
		} else {
			$cartItems = $request->session()->get('cart', []);
			if (empty($cartItems)) {
				return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống, không thể thanh toán.');
			}
			foreach ($cartItems as &$item) {
				$quantity = (int) ($item['qty'] ?? 1);
				if ($quantity < 1) {
					$quantity = 1;
				}
				if ($quantity > 20) {
					$quantity = 20;
				}
				$item['qty'] = $quantity;
			}
			unset($item);
			$request->session()->put('cart', $cartItems);
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
			// Optional when buying a single product quickly; enforce max 20
			'qty' => ['nullable', 'integer', 'min:1', 'max:20'],
		]);

		// Lấy thông tin giỏ hàng
		$cartItems = $request->session()->get('cart', []);

		// Tính toán tổng tiền
		$subtotal = collect($cartItems)->reduce(function ($carry, $item) {
			return $carry + ($item['qty'] * $item['price']);
		}, 0);

		$shipping = $subtotal >= 2000000 ? 0 : 30000;
		$total = $subtotal + $shipping;

		// Tạo đơn hàng
		$order = \App\Models\Order::create([
			'user_id' => $request->user()?->id,
			'order_number' => 'ORD' . time() . mt_rand(1000, 9999),
			'full_name' => $validated['full_name'],
			'email' => $validated['email'],
			'phone' => $validated['phone'],
			'address' => $validated['address'],
			'payment_method' => $validated['payment_method'],
			'subtotal' => $subtotal,
			'shipping' => $shipping,
			'total' => $total,
			'status' => 'pending',
			'items' => $cartItems,
		]);

		// Cập nhật thông tin user nếu đã đăng nhập
		if ($request->user()) {
			$user = $request->user();
			$user->name = $validated['full_name'];
			$user->email = $validated['email'];
			$user->phone = $validated['phone'];
			$user->address = $validated['address'];
			$user->save();
		}

		// Build order record (session-backed). Prefer cart in session; fall back to single-item request params.
		$cartItems = $request->session()->get('cart', []);
		if (empty($cartItems) && $request->filled('name') && $request->filled('price')) {
			// single item buy-now fallback
			$cartItems = [[
				'name' => $request->input('name'),
				'price' => (int) $request->input('price'),
				'qty' => (int) ($request->input('qty', 1)),
				'slug' => $request->input('slug', null),
			]];
		}

		$subtotal = collect($cartItems)->reduce(function ($carry, $item) {
			return $carry + ((int) ($item['qty'] ?? 1) * (int) ($item['price'] ?? 0));
		}, 0);
		$shipping = $subtotal >= 2000000 ? 0 : 30000;
		$total = $subtotal + $shipping;

		$order = [
			'id' => time() . mt_rand(1000, 9999),
			'user_id' => $request->user()?->id ?? null,
			'full_name' => $validated['full_name'],
			'email' => $validated['email'],
			'phone' => $validated['phone'],
			'address' => $validated['address'],
			'payment_method' => $validated['payment_method'],
			'items' => $cartItems,
			'subtotal' => $subtotal,
			'shipping' => $shipping,
			'total' => $total,
			'created_at' => now()->toDateTimeString(),
		];

		$orders = $request->session()->get('orders', []);
		array_unshift($orders, $order); // newest first
		$request->session()->put('orders', $orders);

		// Clear cart after successful order
		$request->session()->forget('cart');

		return redirect()->route('home')
			->with('success', 'Đơn hàng của bạn đã đặt thành công! Mã đơn: #' . $order['id']);
	}
}
