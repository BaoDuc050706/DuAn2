<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mail as OrderMail;

class PaymentController extends Controller
{
	public function index(Request $request): View|RedirectResponse
	{
		// Enforce max 20 units per product
		if ($request->boolean('buy_now')) {
			$name = (string) $request->query('name', 'Sản phẩm');
			$price = (int) $request->query('price', 0);
			$qty = (int) $request->query('qty', 1);
			$productId = (int) $request->query('product_id', 0);
			$slug = (string) $request->query('slug', '');
			
			// Lấy variants nếu có
			$variantRam = (string) $request->query('variant_ram', '');
			$variantSsd = (string) $request->query('variant_ssd', '');
			$variantColor = (string) $request->query('variant_color', '');
			$variantSwitch = (string) $request->query('variant_switch', '');
			
			if ($qty < 1) {
				$qty = 1;
			}
			if ($qty > 20) {
				$qty = 20;
			}
			
			// ✅ Lấy giỏ hàng hiện tại thay vì reset
			$cart = $request->session()->get('cart', []);
			
			// ✅ Tạo key unique từ product_id + variants
			$variantKey = $productId . '|' . $variantRam . '|' . $variantSsd . '|' . $variantColor . '|' . $variantSwitch;
			
			// ✅ Kiểm tra sản phẩm đã có chưa với cùng variants
			$existingIndex = collect($cart)->search(function($item) use ($productId, $variantKey) {
				if ($productId > 0 && isset($item['product_id']) && (int)$item['product_id'] > 0) {
					$itemVariantKey = (isset($item['product_id']) ? $item['product_id'] : 0) . '|' . 
									  (isset($item['variant_ram']) ? $item['variant_ram'] : '') . '|' . 
									  (isset($item['variant_ssd']) ? $item['variant_ssd'] : '') . '|' . 
									  (isset($item['variant_color']) ? $item['variant_color'] : '') . '|' . 
									  (isset($item['variant_switch']) ? $item['variant_switch'] : '');
					return $itemVariantKey === $variantKey;
				}
				return false;
			});
			
			if ($existingIndex !== false) {
				// ✅ Nếu đã có -> tăng số lượng
				$cart[$existingIndex]['qty'] += $qty;
				if ($cart[$existingIndex]['qty'] > 20) {
					$cart[$existingIndex]['qty'] = 20;
				}
			} else {
				// ✅ Nếu chưa có -> thêm mới với đầy đủ variant info
				$cart[] = [
					'product_id' => $productId,
					'name' => $name, 
					'qty' => $qty, 
					'price' => $price,
					'slug' => $slug,
					'variant_ram' => $variantRam,
					'variant_ssd' => $variantSsd,
					'variant_color' => $variantColor,
					'variant_switch' => $variantSwitch,
				];
			}
			
			$cartItems = array_values($cart);
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

		$shipping = $subtotal >= 2000000 ? 0 : 60000;
		$total = $subtotal + $shipping;

		// QR data demo - sử dụng ASCII safe characters để tránh lỗi encoding
		$fakeQrData = 'Ngan hang ABC - STK 123456789 - Thanh toan don hang - Tong tien: ' . $total . 'd';

		return view('checkout', compact('cartItems', 'subtotal', 'shipping', 'total', 'fakeQrData'));
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

		$shipping = $subtotal >= 2000000 ? 0 : 60000;
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
		$shipping = $subtotal >= 2000000 ? 0 : 60000;
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
		// Gửi email xác nhận đơn hàng cho khách
		try {
			Mail::to($validated['email'])->send(new OrderMail($order));
		} catch (\Exception $e) {
			// Có thể ghi log nếu cần
			\Log::error('Lỗi gửi mail xác nhận đơn hàng: ' . $e->getMessage());
		}

		// Thông báo khác nhau tùy theo phương thức thanh toán
		if ($validated['payment_method'] === 'bank') {
			$message = 'Chuyển khoản thành công! Mã đơn: #' . $order['id'] . ' - Cảm ơn bạn đã mua hàng!';
		} elseif ($validated['payment_method'] === 'card') {
			$message = 'Thanh toán bằng thẻ thành công! Mã đơn: #' . $order['id'] . ' - Cảm ơn bạn đã mua hàng!';
		} else {
			$message = 'Đơn hàng của bạn đã đặt thành công! Mã đơn: #' . $order['id'];
		}

		return redirect()->route('home')
			->with('success', $message);
	}
}
