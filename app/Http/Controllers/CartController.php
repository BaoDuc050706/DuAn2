<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Hiển thị giỏ hàng
     */
    public function index(Request $request): View
    {
        $cartItems = $request->session()->get('cart', []);

        $missingImageIds = collect($cartItems)
            ->filter(fn($item) => empty($item['image']) && !empty($item['product_id']))
            ->pluck('product_id')
            ->unique()
            ->filter()
            ->values();

        if ($missingImageIds->isNotEmpty()) {
            $images = Product::whereIn('id', $missingImageIds->all())
                ->pluck('image', 'id');

            foreach ($cartItems as $idx => $item) {
                $pid = $item['product_id'] ?? null;
                if (!empty($pid) && empty($item['image']) && isset($images[$pid])) {
                    $cartItems[$idx]['image'] = $images[$pid];
                }
            }

            $request->session()->put('cart', $cartItems);
        }

        $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['qty']);

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        $productId = (int) $request->input('product_id', 0);
        $slug = $request->input('slug', '');
        $name = (string) $request->input('name', '');
        $price = (int) $request->input('price', 0);
        $image = (string) $request->input('image', '');

        $productModel = null;
        if ($productId > 0) {
            $productModel = Product::find($productId);
        }
        if (empty($image) && $productModel) {
            $image = (string) ($productModel->image ?? '');
        }

        // Lấy các variant nếu có
        $variantRam = $request->input('variant_ram', '');
        $variantSsd = $request->input('variant_ssd', '');
        $variantColor = $request->input('variant_color', '');
        $variantSwitch = $request->input('variant_switch', '');

        $cart = $request->session()->get('cart', []);

        // ✅ Tạo key unique từ product_id + variants
        $variantKey = $productId . '|' . $variantRam . '|' . $variantSsd . '|' . $variantColor . '|' . $variantSwitch;

        // ✅ Kiểm tra theo product_id + variants
        $existingIndex = collect($cart)->search(function ($item) use ($productId, $name, $price, $variantKey) {
            if ($productId > 0 && isset($item['product_id']) && (int)$item['product_id'] > 0) {
                // Kiểm tra product_id và variants
                $itemVariantKey = (isset($item['product_id']) ? $item['product_id'] : 0) . '|' .
                    (isset($item['variant_ram']) ? $item['variant_ram'] : '') . '|' .
                    (isset($item['variant_ssd']) ? $item['variant_ssd'] : '') . '|' .
                    (isset($item['variant_color']) ? $item['variant_color'] : '') . '|' .
                    (isset($item['variant_switch']) ? $item['variant_switch'] : '');
                return $itemVariantKey === $variantKey;
            }
            // Fallback: kiểm tra theo name và price
            if (!empty($name) && (int)$item['price'] === $price) {
                return $item['name'] === $name;
            }
            return false;
        });

        if ($existingIndex !== false) {
            // ✅ Nếu đã có -> tăng số lượng
            $addQty = (int) $request->input('qty', 1);
            $desiredQty = $cart[$existingIndex]['qty'] + $addQty;

            // If product_id available, enforce stock limits
            if ($productModel) {
                $available = (int) $productModel->stock;
                if ($desiredQty > $available) {
                    // If strict mode requested, return error
                    if ($request->boolean('strict_stock')) {
                        return redirect()->back()->with('error', 'Số lượng vượt quá tồn kho. Còn ' . $available . ' sản phẩm.');
                    }

                    // Otherwise cap to available and notify
                    $cart[$existingIndex]['qty'] = $available;
                    $request->session()->put('cart', array_values($cart));
                    return redirect()->route('cart.index')->with('warning', 'Số lượng sản phẩm đã được điều chỉnh về tồn kho hiện có: ' . $available);
                }
            }

            $cart[$existingIndex]['qty'] = $desiredQty;
        } else {
            // ✅ Nếu chưa có -> thêm mới
            $initialQty = (int) $request->input('qty', 1);
            // If product_id present, enforce stock limits when adding
            if ($productModel) {
                $available = (int) $productModel->stock;
                if ($initialQty > $available) {
                    if ($request->boolean('strict_stock')) {
                        return redirect()->back()->with('error', 'Số lượng vượt quá tồn kho. Còn ' . $available . ' sản phẩm.');
                    }
                    // cap to available
                    $initialQty = $available;
                    // notify user
                    $request->session()->flash('warning', 'Số lượng sản phẩm đã được điều chỉnh về tồn kho hiện có: ' . $available);
                }
            }

            $cart[] = [
                'product_id' => $productId,
                'name'       => $name,
                'price'      => $price,
                'qty'        => $initialQty,
                'slug'       => $slug,
                'variant_ram' => $variantRam,
                'variant_ssd' => $variantSsd,
                'variant_color' => $variantColor,
                'variant_switch' => $variantSwitch,
                'image'       => $image,
            ];
        }

        // ✅ RẤT QUAN TRỌNG: reset key để tránh tách sản phẩm
        $cart = array_values($cart);

        // ✅ Lưu lại giỏ hàng
        $request->session()->put('cart', $cart);

        // Kiểm tra nếu là AJAX request thì return JSON
        if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'cart_count' => count($cart)
            ]);
        }

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
            $productId = isset($cart[$index]['product_id']) ? (int)$cart[$index]['product_id'] : 0;
            $newQty = $cart[$index]['qty'] + 1;
            if ($productId > 0) {
                $prod = Product::find($productId);
                if ($prod) {
                    $available = (int)$prod->stock;
                    if ($newQty > $available) {
                        // cap and flash
                        $cart[$index]['qty'] = $available;
                        $request->session()->put('cart', $cart);
                        return redirect()->route('cart.index')->with('warning', 'Không thể tăng. Chỉ còn ' . $available . ' sản phẩm trong kho.');
                    }
                }
            }

            $cart[$index]['qty'] = $newQty;
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
