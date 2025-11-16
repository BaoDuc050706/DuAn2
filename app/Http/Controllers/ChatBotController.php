<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ChatBotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatBotController extends Controller
{
    public function __construct(
        private readonly ChatBotService $chatBotService,
    ) {
    }

    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $cartItems = $request->session()->get('cart', []);
        $cartTotal = collect($cartItems)->reduce(fn ($carry, $item) => $carry + ((int) ($item['qty'] ?? 1) * (int) ($item['price'] ?? 0)), 0);
        $cartSummary = $cartTotal > 0
            ? sprintf('Có %d sản phẩm với tổng giá trị ~%sđ.', collect($cartItems)->sum('qty'), number_format($cartTotal, 0, ',', '.'))
            : '';

        $featured = Product::query()
            ->select(['name', 'price', 'slug'])
            ->latest()
            ->limit(3)
            ->get();

        $analysis = $this->analyzeMessage($validated['message']);
        $matchedProducts = $this->fetchMatchedProducts($analysis);
        $selectedProduct = $this->pickSelectedProduct($validated['message'], $matchedProducts, $analysis);

        $reply = $this->chatBotService->reply($validated['message'], [
            'featured_products' => $featured,
            'cart_summary' => $cartSummary,
            'user_name' => $request->user()?->name,
            'matched_products' => $matchedProducts,
            'intent' => $analysis['intent'],
            'category_label' => $analysis['category_label'],
            'order_intent' => $analysis['order_intent'],
            'selected_product' => $selectedProduct,
        ]);

        return response()->json([
            'reply' => $reply,
        ]);
    }

    /**
     * @return array{intent:string|null, category_slug:string|null, category_label:string|null, order_intent:bool}
     */
    private function analyzeMessage(string $message): array
    {
        $text = Str::lower($message);

        $intent = null;
        $categorySlug = null;
        $categoryLabel = null;

        $bestSellerKeywords = ['bán chạy', 'bán chạy nhất', 'best seller', 'hot nhất', 'được mua nhiều'];
        $cheapKeywords = ['giá rẻ', 'rẻ nhất', 'tiết kiệm', 'giảm giá'];

        foreach ($bestSellerKeywords as $keyword) {
            if (Str::contains($text, $keyword)) {
                $intent = 'best_seller';
                break;
            }
        }

        if (!$intent) {
            foreach ($cheapKeywords as $keyword) {
                if (Str::contains($text, $keyword)) {
                    $intent = 'cheapest';
                    break;
                }
            }
        }

        $categoryMap = [
            'tai-nghe' => ['label' => 'Tai nghe', 'keywords' => ['tai nghe', 'tai-nghe', 'headphone', 'headset']],
            'ban-phim' => ['label' => 'Bàn phím', 'keywords' => ['ban phim', 'bàn phím', 'keyboard']],
            'laptop' => ['label' => 'Laptop', 'keywords' => ['laptop', 'notebook']],
            'loa' => ['label' => 'Loa', 'keywords' => ['loa', 'speaker']],
            'chuot' => ['label' => 'Chuột', 'keywords' => ['chuột', 'chuot', 'mouse']],
        ];

        foreach ($categoryMap as $slug => $data) {
            foreach ($data['keywords'] as $keyword) {
                if (Str::contains($text, $keyword)) {
                    $categorySlug = $slug;
                    $categoryLabel = $data['label'];
                    break 2;
                }
            }
        }

        if (!$intent && $categorySlug) {
            $intent = 'category_recommendation';
        }

        $orderIntent = $this->containsAny($text, [
            'chốt',
            'chot',
            'chot don',
            'đặt',
            'dat',
            'đặt hàng',
            'dat hang',
            'mua',
            'mua ngay',
            'book',
            'đóng đơn',
        ]);

        return [
            'intent' => $intent,
            'category_slug' => $categorySlug,
            'category_label' => $categoryLabel,
            'order_intent' => $orderIntent,
        ];
    }

    /**
     * @param  array{intent:string|null, category_slug:string|null}  $analysis
     */
    private function fetchMatchedProducts(array $analysis)
    {
        if (!$analysis['intent'] && !$analysis['category_slug']) {
            return collect();
        }

        $query = Product::query()
            ->select(['name', 'price', 'slug'])
            ->when($analysis['category_slug'], function ($builder, $slug) {
                $builder->whereHas('category', fn ($q) => $q->where('slug', $slug));
            });

        if ($analysis['intent'] === 'cheapest') {
            $query->orderBy('price');
        } elseif ($analysis['intent'] === 'best_seller') {
            $query->latest('updated_at');
        } else {
            $query->latest();
        }

        return $query->limit(3)->get();
    }

    private function pickSelectedProduct(string $message, $matchedProducts, array $analysis)
    {
        if (!$analysis['order_intent']) {
            return null;
        }

        if (!$matchedProducts instanceof \Illuminate\Support\Collection || $matchedProducts->isEmpty()) {
            return null;
        }

        $index = $this->extractProductIndex($message);

        if ($index !== null && $index >= 1 && $index <= $matchedProducts->count()) {
            return $this->mapProduct($matchedProducts[$index - 1]);
        }

        return $this->mapProduct($matchedProducts->first());
    }

    private function extractProductIndex(string $message): ?int
    {
        if (preg_match('/\b([1-3])\b/u', $message, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function containsAny(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (Str::contains($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function mapProduct($product): ?array
    {
        if (!$product) {
            return null;
        }

        return [
            'name' => $product->name,
            'price' => (int) $product->price,
            'slug' => $product->slug,
        ];
    }
}

