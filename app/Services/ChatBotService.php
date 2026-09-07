<?php

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatBotService
{
    private ?\OpenAI\Client $client = null;

    public function __construct(
        private readonly ?string $apiKey = null,
        private readonly string $model = 'gpt-4o-mini',
        private readonly float $temperature = 0.3,
        private readonly int $maxTokens = 400,
    ) {
        $key = $apiKey ?? (string) config('services.openai.key');

        if (!empty($key)) {
            $this->client = \OpenAI::client($key);
        }
    }

    /**
     * @param  array{featured_products?: \Illuminate\Support\Collection|array, matched_products?: \Illuminate\Support\Collection|array, cart_summary?: string|null, user_name?: string|null, intent?: string|null, category_label?: string|null, order_intent?: bool|null, selected_product?: array|null}  $context
     */
    public function reply(string $message, array $context = []): string
    {
        if (!$this->client) {
            return $this->basicReply($message, $context);
        }

        $systemPrompt = $this->buildSystemPrompt($context);

        try {
            $response = $this->client->responses()->create([
                'model' => config('services.openai.model', $this->model),
                'temperature' => (float) config('services.openai.temperature', $this->temperature),
                'max_output_tokens' => (int) config('services.openai.max_tokens', $this->maxTokens),
                'input' => [
                    [
                        'role' => 'system',
                        'content' => $systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $message,
                    ],
                ],
            ]);

            $text = $response->output[0]->content[0]->text ?? null;

            return $text ? trim($text) : 'Xin lỗi, tôi chưa thể trả lời câu hỏi đó lúc này.';
        } catch (\Throwable $e) {
            Log::error('Chatbot error: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return 'Xin lỗi, máy chủ chatbot đang gặp sự cố. Vui lòng thử lại sau ít phút.';
        }
    }

    /**
     * @param  array{featured_products?: \Illuminate\Support\Collection|array, matched_products?: \Illuminate\Support\Collection|array, cart_summary?: string|null, user_name?: string|null, order_intent?: bool|null, selected_product?: array|null}  $context
     */
    private function buildSystemPrompt(array $context): string
    {
        /** @var Collection<int, array{name:string,price:int,slug?:string}>|array|null $featured */
        $featured = $context['featured_products'] ?? [];
        if ($featured instanceof Collection) {
            $featured = $featured->toArray();
        }

        $cartSummary = (string) ($context['cart_summary'] ?? '');
        $userName = (string) ($context['user_name'] ?? '');

        $lines = [
            'Bạn là trợ lý bán hàng thân thiện cho CAEKT Gear Store tại Việt Nam.',
            'Trả lời ngắn gọn, rõ ràng, ưu tiên tiếng Việt và đề xuất sản phẩm phù hợp.',
            'Nếu khách hỏi về tình trạng đơn hàng, hãy hướng dẫn họ dùng mục "Tra cứu đơn hàng" hoặc đăng nhập xem lịch sử mua.',
            'Không bịa thông tin nếu không chắc chắn; hãy đề xuất khách liên hệ hotline 1900.5301 khi cần.',
        ];

        if ($userName !== '') {
            $lines[] = 'Khách hàng hiện tại: '.$userName.'. Hãy xưng hô thân thiện.';
        }

        if (!empty($featured)) {
            $productLines = collect($featured)
                ->map(function ($item, $index) {
                    $name = Arr::get($item, 'name', 'Sản phẩm');
                    $price = number_format((int) Arr::get($item, 'price', 0), 0, ',', '.');
                    $slug = Arr::get($item, 'slug');
                    $link = $slug ? url('/product/'.$slug) : url('/');

                    return ($index + 1).". {$name} - {$price}đ ({$link})";
                })
                ->implode("\n");

            $lines[] = "Sản phẩm nổi bật tuần này:\n".$productLines;
        }

        if ($cartSummary !== '') {
            $lines[] = 'Tóm tắt giỏ hàng hiện tại của khách: '.$cartSummary;
        }

        $matched = $context['matched_products'] ?? [];
        if ($matched instanceof Collection) {
            $matched = $matched->toArray();
        }

        if (!empty($matched)) {
            $lines[] = "Gợi ý theo nhu cầu khách:\n".$this->formatProductListing($matched);
        }

        if (!empty($context['order_intent']) && !empty($context['selected_product'])) {
            $product = $context['selected_product'];
            $lines[] = 'Khách muốn đặt '.$product['name'].' với link '.($product['slug'] ? url('/product/'.$product['slug']) : url('/')).'. Hãy hướng dẫn từng bước đặt hàng.';
        }

        return implode("\n", $lines);
    }

    /**
     * Simple keyword-based fallback answers when OpenAI isn't configured.
     */
    private function basicReply(string $message, array $context): string
    {
        $intent = $context['intent'] ?? null;

        if (!empty($context['order_intent'])) {
            return $this->buildOrderResponse($context);
        }

        if ($intent === 'best_seller') {
            return $this->buildBestSellerResponse($context);
        }

        if ($intent === 'cheapest') {
            return $this->buildCheapestResponse($context);
        }

        if ($this->hasProducts($context['matched_products'] ?? null)) {
            return $this->buildCategoryResponse($context);
        }

        $normalized = Str::of($message)->lower();

        if ($this->containsAny($normalized, ['cảm ơn', 'cam on', 'thank you', 'thanks'])) {
            return $this->randomResponse([
                'Rất vui được hỗ trợ bạn! Nếu cần tư vấn thêm sản phẩm hay kiểm tra đơn hàng, cứ nhắn cho mình nhé.',
                'Cảm ơn bạn nữa nhé! Khi nào cần thêm thông tin cứ gọi mình.',
                'Hân hạnh được giúp bạn. Có gì cứ hỏi tiếp nha!',
            ]);
        }

        if ($this->containsAny($normalized, ['đặt nhầm', 'dat nham', 'order nhầm', 'wrong order'])) {
            return $this->randomResponse([
                'Nếu bạn đặt nhầm sản phẩm, hãy liên hệ hotline 1900.5301 hoặc trả lời email xác nhận để bên mình hỗ trợ chỉnh/cancel nhanh nhất.',
                'Đặt nhầm cũng không sao: bạn gọi 1900.5301 hoặc chat lại với thông tin đơn để mình hủy/sửa giúp.',
                'Bạn có thể vào mục Tra cứu đơn hàng hoặc liên hệ trực tiếp đội chăm sóc (hotline 1900.5301) để cập nhật đơn đặt nhầm nhé.',
            ]);
        }

        if ($this->containsAny($normalized, ['không', 'khong', 'ko', 'nope', 'no'])) {
            return $this->randomResponse([
                'Không sao đâu, khi nào bạn cần đặt hàng hay tư vấn thêm cứ gọi mình nhé!',
                'Ok, bạn cứ cân nhắc thêm. Khi cần mình luôn sẵn sàng.',
                'Hiểu rồi, bạn cứ thong thả - cần hỗ trợ thêm cứ nhắn.',
            ]);
        }

        if ($this->containsAny($normalized, ['có', 'co', 'ok', 'oke', 'yes', 'tất nhiên'])) {
            return $this->randomResponse([
                'Tuyệt! Bạn mở giỏ hàng và bấm "Tiến hành thanh toán", điền địa chỉ và chọn COD hoặc chuyển khoản. Sau khi đặt hàng thành công, thông tin đơn hàng sẽ được gửi tới email bạn đã đăng ký. Nếu cần mình hỗ trợ thêm cứ nhắn tiếp nhé.',
                'Ok, bạn vào giỏ hàng -> Tiến hành thanh toán, điền thông tin rồi xác nhận phương thức. Sau khi hoàn tất, bạn sẽ nhận email xác nhận đơn hàng tại địa chỉ email đã đăng ký. Cần trợ giúp thêm cứ nói nhé.',
                'Tuyệt! Bạn bấm "Mua ngay"/Giỏ hàng để thanh toán, điền địa chỉ + chọn COD hoặc chuyển khoản. Thông tin đơn hàng sẽ được gửi qua email sau khi đặt hàng thành công, bạn nhớ kiểm tra hộp thư nhé. Cần trợ giúp thêm cứ nói.',
            ]);
        }

        if ($this->containsAny($normalized, ['bạn là ai', 'người tạo ra bạn', 'nguoi tao ra ban', 'ai tạo ra bạn', 'who made you'])) {
            return 'Mình là chat bot của CAEKT Gear Store và được tạo bởi Trần Nguyên Đức Bảo ^^.';
        }

        if ($this->containsAny($normalized, ['ai là người tạo ra trang web','đội phát triển', 'những người tạo ra trang web', 'team tạo web', 'ai làm trang web', 'team dev'])) {
            return "Đội phát triển trang web gồm:\n- Trần Nguyên Đức Bảo (Nhóm trưởng)\n- Nguyễn Quốc Anh(Thành viên)\n- Trần Ngọc Hoàng Anh(Thành viên)\n- Lê Chí Bảo(Thành viên)";
        }

        if ($this->containsAny($normalized, ['tư vấn', 'tu van', 'support'])) {
            return $this->randomResponse([
                'Bạn cần tư vấn sản phẩm gì ? (ví dụ laptop gaming, tai nghe không dây, PC làm việc)? Mình sẽ gợi ý cấu hình và giá phù hợp nhu cầu cũng như ngân sách của bạn.',
                'Cho mình biết nhu cầu cụ thể (gaming, học tập, văn phòng, tầm giá…) để mình lọc sản phẩm chuẩn nhất nhé.',
                'Bạn muốn tìm loại sản phẩm nào? Cứ mô tả nhu cầu và ngân sách, mình sẽ tư vấn cấu hình phù hợp.',
            ]);
        }

        if ($this->containsAny($normalized, ['vận chuyển', 'ship', 'giao hàng', 'phí ship'])) {
            return $this->randomResponse([
                'Đơn từ 2 triệu được miễn phí vận chuyển toàn quốc. Đơn nhỏ hơn thu 30.000đ, thời gian giao 1-3 ngày nội thành và 3-5 ngày liên tỉnh.',
                'Phí ship: miễn phí với đơn ≥ 2 triệu, còn lại 30.000đ. Giao nội thành 1-3 ngày, liên tỉnh 3-5 ngày.',
                'Bên mình miễn phí ship cho đơn 2 triệu trở lên, dưới mức đó phụ phí 30k. Thời gian giao từ 1-5 ngày tùy khu vực.',
            ]);
        }

        if ($this->containsAny($normalized, ['thanh toán', 'payment', 'trả góp', 'credit'])) {
            return $this->randomResponse([
                'Bạn có thể thanh toán COD, chuyển khoản ngân hàng hoặc thẻ. Trả góp lãi suất 0% đang hoàn thiện, tạm thời hỗ trợ qua thẻ tín dụng tại quầy.',
                'Hiện hỗ trợ COD, chuyển khoản và quẹt thẻ. Trả góp 0% sẽ cập nhật sớm, tạm thời xử lý trực tiếp qua thẻ tín dụng.',
                'Phương thức thanh toán gồm COD, chuyển khoản và thẻ. Nếu cần trả góp bạn báo trước để mình hướng dẫn.',
            ]);
        }

        if ($this->containsAny($normalized, ['đơn hàng', 'tra cứu', 'order status'])) {
            return $this->randomResponse([
                'Bạn vào mục "Tra cứu đơn hàng" ở header hoặc đăng nhập và mở "Lịch sử mua hàng". Thông tin đơn hàng cũng được gửi tới email bạn đã đăng ký khi đặt hàng, bạn có thể kiểm tra hộp thư để xem chi tiết. Nếu cần hỗ trợ nhanh hãy gọi Hotline 1900.5301.',
                'Để xem đơn, bạn bấm "Tra cứu đơn hàng" hoặc đăng nhập → "Lịch sử mua hàng". Ngoài ra, thông tin đơn hàng đã được gửi qua email sau khi bạn đặt hàng thành công, bạn nhớ kiểm tra email nhé. Cần gấp gọi hotline 1900.5301.',
                'Bạn có thể tự tra cứu đơn ở mục cùng tên trên header hoặc vào lịch sử mua hàng sau khi đăng nhập. Thông tin đơn hàng cũng được gửi tới email của bạn, hãy kiểm tra hộp thư để xem chi tiết đơn hàng.',
            ]);
        }

        if ($this->containsAny($normalized, ['bảo hành', 'warranty'])) {
            return $this->randomResponse([
                'Tất cả sản phẩm chính hãng bảo hành tối thiểu 12 tháng. Bạn giữ hóa đơn điện tử và số serial để được hỗ trợ tại trung tâm hoặc bên mình, mọi thông tin bạn có thể liên hệ hotline 1900.5301 để được hỗ trợ.',
                'Sản phẩm ở đây đều là hàng chính hãng, bảo hành ít nhất 12 tháng. Bạn nhớ lưu hóa đơn và serial để tiện xử lý, mọi thông tin bạn có thể liên hệ hotline 1900.5301 để được hỗ trợ.',
                'Bảo hành tối thiểu 12 tháng, tùy dòng có thể 24-36 tháng. Chỉ cần cung cấp serial + thông tin đặt hàng là được hỗ trợ, mọi thông tin bạn có thể liên hệ hotline 1900.5301 để được hỗ trợ.',
            ]);
        }

        if (!empty($context['cart_summary'])) {
            return $context['cart_summary'].' Bạn muốn chốt đơn luôn chứ? Mình có thể hướng dẫn thanh toán/ship ngay.';
        }

        return $this->buildGenericResponse($context);
    }

    private function containsAny(Str|string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (Str::of($haystack)->contains(Str::lower($needle))) {
                return true;
            }
        }

        return false;
    }

    private function buildBestSellerResponse(array $context): string
    {
        $products = $this->hasProducts($context['matched_products'] ?? null)
            ? $context['matched_products']
            : ($context['featured_products'] ?? null);
        $list = $this->formatProductListing($products);
        if ($list === '') {
            return 'Hiện mình đang cập nhật danh sách sản phẩm bán chạy. Bạn có thể duyệt danh mục hoặc chat hotline 1900.5301 để được tư vấn ngay.';
        }

        return "Top sản phẩm đang bán chạy:\n".$list."\nBạn muốn so sánh thêm hay đặt nhanh sản phẩm nào?";
    }

    private function buildGenericResponse(array $context): string
    {
        $list = $this->formatProductListing($context['featured_products'] ?? null, 2);
        $intro = 'Mình sẵn sàng hỗ trợ tư vấn cấu hình, báo giá và theo dõi đơn hàng.';

        if ($list !== '') {
            $intro .= "\nGợi ý nổi bật: \n".$list;
        }

        $intro .= "\nBạn cứ cho mình biết nhu cầu (gaming, văn phòng, laptop mỏng nhẹ...) để mình lọc đúng sản phẩm nhé!";

        return $intro;
    }

    private function buildCategoryResponse(array $context): string
    {
        $label = $context['category_label'] ?? 'danh mục này';
        $list = $this->formatProductListing($context['matched_products'] ?? null);
        if ($list === '') {
            return 'Mình đang cập nhật danh sách cho '.$label.'. Bạn thử xem danh mục trên trang hoặc gọi hotline để được báo giá nhanh.';
        }

        return "Top lựa chọn về {$label}:\n{$list}\nBạn cần mình so sánh thêm hay tư vấn cấu hình phù hợp?";
    }

    private function buildCheapestResponse(array $context): string
    {
        $label = $context['category_label'] ?? 'các sản phẩm';
        $products = $this->hasProducts($context['matched_products'] ?? null)
            ? $context['matched_products']
            : ($context['featured_products'] ?? null);
        $list = $this->formatProductListing($products);

        if ($list === '') {
            return 'Hiện chưa có danh sách giá rẻ để gợi ý. Bạn xem mục khuyến mãi hoặc chờ mình cập nhật thêm nhé.';
        }

        return "Các lựa chọn giá tốt cho {$label}:\n{$list}\nBạn muốn đặt ngay hay cần thêm thông tin chi tiết?";
    }

    private function formatProductListing(mixed $products, int $limit = 3): string
    {
        if (!$this->hasProducts($products)) {
            return '';
        }

        if ($products instanceof Collection) {
            $products = $products->toArray();
        }

        return collect($products)
            ->take($limit)
            ->map(function ($item, $index) {
                $name = Arr::get($item, 'name', 'Sản phẩm');
                $price = number_format((int) Arr::get($item, 'price', 0), 0, ',', '.');
                $slug = Arr::get($item, 'slug');
                $link = $slug ? url('/product/'.$slug) : url('/');

                return ($index + 1).". {$name} - {$price}đ ({$link})";
            })
            ->implode("\n");
    }

    private function buildOrderResponse(array $context): string
    {
        $product = $context['selected_product'] ?? null;

        if ($product) {
            $name = $product['name'] ?? 'sản phẩm bạn chọn';
            $link = !empty($product['slug']) ? url('/product/'.$product['slug']) : url('/');
            $price = isset($product['price']) ? number_format((int) $product['price'], 0, ',', '.') : null;
            $priceText = $price ? " (giá tham khảo {$price}đ)" : '';

            return "Bạn muốn chốt {$name}{$priceText}. Bạn mở {$link}, chọn \"Mua ngay\" hoặc \"Thêm vào giỏ\", sau đó vào Giỏ hàng → Tiến hành thanh toán. Điền địa chỉ/phone rồi chọn phương thức thanh toán (COD hoặc chuyển khoản). Sau khi đặt hàng thành công, thông tin đơn hàng sẽ được gửi tới email bạn đã đăng ký, bạn nhớ kiểm tra hộp thư nhé. Nếu cần hỗ trợ thao tác, gọi hotline 1900.5301 nhé!";
        }

        return 'Để đặt hàng bạn chọn sản phẩm muốn mua, bấm "Mua ngay" (hoặc thêm vào giỏ), rồi làm theo hướng dẫn thanh toán. Sau khi đặt hàng thành công, thông tin đơn hàng sẽ được gửi tới email bạn đã đăng ký, bạn nhớ kiểm tra hộp thư nhé. Nếu cần mình hướng dẫn chi tiết hơn hãy mô tả tên sản phẩm nhé.';
    }

    private function randomResponse(array $options): string
    {
        return Arr::random($options);
    }

    private function hasProducts(mixed $products): bool
    {
        if (empty($products)) {
            return false;
        }

        if ($products instanceof Collection) {
            return $products->isNotEmpty();
        }

        return !empty($products);
    }
}

