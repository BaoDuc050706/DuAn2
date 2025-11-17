@extends('layouts.app')

@section('title', $product->name . ' - Thông tin sản phẩm')

@section('content')
    <div class="row g-4">
        <div class="col-md-6">
 <img src="{{ asset('image/' . $product->image) }}" 
     alt="{{ $product->name }}" 
     class="img-fluid">



        </div>
        <div class="col-md-6">
            <h1 class="h3 mb-3">{{ $product->name }}</h1>
            <div class="h4 text-danger mb-3">{{ number_format($product->price, 0, ',', '.') }}₫</div>
            <p class="text-muted">{{ $product->description }}</p>

            {{-- Options tùy theo loại sản phẩm --}}
            <form id="addToCartForm" action="{{ route('cart.add') }}" method="POST" class="my-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="name" value="{{ $product->name }}">
                <input type="hidden" name="price" id="final_price" value="{{ (int)$product->price }}">
                <input type="hidden" name="slug" value="{{ $product->slug }}">
                
                <div class="mb-3">
                    <h5>Giá: <span class="text-danger" id="display_price">{{ number_format($product->price, 0, ',', '.') }}₫</span></h5>
                </div>
                
                {{-- Options cho Laptop --}}
                @if($product->category && strtolower($product->category->name) === 'laptop')
                    <div class="mb-3">
                        <label class="form-label">Chọn dung lượng RAM</label>
                        <select name="variant_ram" class="form-select variant-select">
                            <option value="">-- Chọn RAM --</option>
                            <option value="16gb" data-price="0">16GB DDR5 (+0₫)</option>
                            <option value="32gb" data-price="3000000">32GB DDR5 (+3,000,000₫)</option>
                            <option value="64gb" data-price="6000000">64GB DDR5 (+6,000,000₫)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn dung lượng SSD</label>
                        <select name="variant_ssd" class="form-select variant-select">
                            <option value="">-- Chọn SSD --</option>
                            <option value="512gb" data-price="0">512GB SSD (+0₫)</option>
                            <option value="1tb" data-price="2000000">1TB SSD (+2,000,000₫)</option>
                            <option value="2tb" data-price="5000000">2TB SSD (+5,000,000₫)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn màu sắc</label>
                        <select name="variant_color" class="form-select">
                            <option value="">-- Chọn màu --</option>
                            <option value="silver">Bạc (Silver)</option>
                            <option value="black">Đen (Black)</option>
                            <option value="gold">Vàng (Gold)</option>
                            <option value="gray">Xám (Gray)</option>
                        </select>
                    </div>
                
                {{-- Options cho Bàn phím --}}
                @elseif($product->category && strtolower($product->category->name) === 'bàn phím')
                    <div class="mb-3">
                        <label class="form-label">Chọn màu sắc</label>
                        <select name="variant_color" class="form-select">
                            <option value="">-- Chọn màu --</option>
                            <option value="black">Đen (Black)</option>
                            <option value="white">Trắng (White)</option>
                            <option value="gray">Xám (Gray)</option>
                            <option value="rgb">RGB Custom</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn loại phím</label>
                        <select name="variant_switch" class="form-select">
                            <option value="">-- Chọn switch --</option>
                            <option value="cherry_mx">Cherry MX</option>
                            <option value="gateron">Gateron</option>
                            <option value="membrane">Membrane (Văn phòng)</option>
                        </select>
                    </div>
                
                {{-- Options cho Chuột --}}
                @elseif($product->category && strtolower($product->category->name) === 'chuột')
                    <div class="mb-3">
                        <label class="form-label">Chọn màu sắc</label>
                        <select name="variant_color" class="form-select">
                            <option value="">-- Chọn màu --</option>
                            <option value="black">Đen (Black)</option>
                            <option value="white">Trắng (White)</option>
                            <option value="gray">Xám (Gray)</option>
                            <option value="rgb">RGB</option>
                        </select>
                    </div>
                
                {{-- Options cho Tai nghe --}}
                @elseif($product->category && strtolower($product->category->name) === 'tai nghe')
                    {{-- Tai nghe không có option chọn, hiển thị thông báo --}}
                    <div class="alert alert-info mb-3">
                        <small><i class="bi bi-info-circle me-2"></i>Sản phẩm này không có các tùy chọn khác. Vui lòng chọn số lượng phía dưới.</small>
                    </div>
                @endif
                
                <div class="mb-3">
                    <label class="form-label">Số lượng</label>
                    <input type="number" name="qty" class="form-control" value="1" min="1" max="20" style="width: 100px;">
                </div>
                
                <button type="submit" class="btn btn-dark" id="addToCartBtn">
                    <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
                </button>
            </form>

            <div class="d-flex gap-2 mt-3">
                <button class="btn btn-danger" id="buyNowBtn">
                    <i class="bi bi-lightning-fill"></i> Mua ngay
                </button>
            </div>

            <hr class="my-4">
            <div class="small text-muted">
                - Giao nhanh 2h nội thành • Bảo hành chính hãng • Đổi trả 7 ngày
            </div>
        </div>
    </div>

    <section class="mt-5">
        <h2 class="h5 mb-3">Mô tả sản phẩm</h2>
        <p>{{ $product->description }}</p>
    </section>

    <section class="mt-5">
        <h2 class="h5 mb-3">Thông số kỹ thuật</h2>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <tbody>
                    @if($product->category && strtolower($product->category->name) === 'laptop')
                        {{-- Thông số cho Laptop --}}
                        <tr>
                            <td class="fw-semibold" style="width: 30%;">Bộ xử lý (CPU)</td>
                            <td>Intel Core i7/i9 hoặc AMD Ryzen 7/9 - Thế hệ mới nhất</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">RAM</td>
                            <td>16GB - 32GB DDR5, có thể nâng cấp</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Ổ cứng (SSD)</td>
                            <td>512GB - 1TB NVMe SSD (tốc độ cao)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Card đồ họa</td>
                            <td>NVIDIA RTX 4060/4070 hoặc tương đương</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Màn hình</td>
                            <td>15.6" - 17" IPS, 2K/4K, tần số quét 120Hz+</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Dung lượng pin</td>
                            <td>50Wh - 80Wh, thời gian sử dụng 8-10 giờ</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Cân nặng</td>
                            <td>1.5kg - 2.5kg (tùy kích thước)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Kết nối</td>
                            <td>WiFi 6E, Bluetooth 5.3, USB-C Thunderbolt 4, HDMI 2.1</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Hệ điều hành</td>
                            <td>Windows 11 Pro / macOS mới nhất</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Bảo hành</td>
                            <td>12-24 tháng bảo hành chính hãng</td>
                        </tr>
                    @elseif($product->category && strtolower($product->category->name) === 'tai nghe')
                        {{-- Thông số cho Tai nghe --}}
                        <tr>
                            <td class="fw-semibold" style="width: 30%;">Kiểu dáng</td>
                            <td>Over-ear / On-ear / True wireless</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Driver</td>
                            <td>40mm - 50mm, công nghệ âm thanh cao cấp</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Tần số</td>
                            <td>20Hz - 20kHz (toàn dải nghe thấy)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Dung lượng pin</td>
                            <td>Sạc 1 lần dùng 20-40 giờ, True wireless 5-10 giờ</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Kết nối</td>
                            <td>Bluetooth 5.3, Jack 3.5mm (nếu có)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Chống ồn</td>
                            <td>Chống ồn chủ động (ANC), cách âm tự động</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Cân nặng</td>
                            <td>150g - 300g (tùy kiểu dáng)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Thời gian sạc</td>
                            <td>1.5 - 2 giờ để sạc đầy</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Khả năng chịu nước</td>
                            <td>IPX4 - IPX5 (chống mồ hôi và nước)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Bảo hành</td>
                            <td>12 tháng bảo hành chính hãng</td>
                        </tr>
                    @elseif($product->category && strtolower($product->category->name) === 'chuột')
                        {{-- Thông số cho Chuột --}}
                        <tr>
                            <td class="fw-semibold" style="width: 30%;">Loại kết nối</td>
                            <td>Wireless / Có dây / Bluetooth</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Cảm biến</td>
                            <td>Optical/Laser, DPI tối đa 6000-16000</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Độ chính xác</td>
                            <td>± 0.2mm, tracking speed cao</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Nút bấm</td>
                            <td>5-12 nút, có thể lập trình</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Dung lượng pin</td>
                            <td>Wireless: 30-50 giờ / sạc (nếu có)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Cân nặng</td>
                            <td>80g - 150g (lightweight design)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Bề mặt</td>
                            <td>Ergonomic grip, chống mồ hôi</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Tính năng RGB</td>
                            <td>RGB LED tuỳ chỉnh màu sắc</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Khả năng chịu nước</td>
                            <td>IP67 (chống bụi, chống nước)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Bảo hành</td>
                            <td>12 tháng bảo hành chính hãng</td>
                        </tr>
                    @elseif($product->category && strtolower($product->category->name) === 'bàn phím')
                        {{-- Thông số cho Bàn phím --}}
                        <tr>
                            <td class="fw-semibold" style="width: 30%;">Loại kết nối</td>
                            <td>Có dây USB / Wireless / Bluetooth</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Loại phím</td>
                            <td>Phím cơ (Mechanical) / Phím văn phòng (Membrane)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Switch</td>
                            <td>Cherry MX / Gateron / Outemu (nếu cơ)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Chất liệu</td>
                            <td>Nhựa ABS / PBT (bền hơn), có hoặc không có rest tựa tay</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Bố cục phím</td>
                            <td>Full Size (104 phím) / TKL (87 phím) / Compact</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Tính năng LED</td>
                            <td>RGB LED per-key / Single color / Không có LED</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Dung lượng pin</td>
                            <td>Wireless: 20-40 giờ / sạc (nếu có)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Cân nặng</td>
                            <td>800g - 2kg (tùy kích thước)</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Tính năng lập trình</td>
                            <td>Phím Macro, có thể tùy chỉnh</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Bảo hành</td>
                            <td>12 tháng bảo hành chính hãng</td>
                        </tr>
                    @else
                        {{-- Thông số mặc định cho các sản phẩm khác --}}
                        <tr>
                            <td class="fw-semibold" style="width: 30%;">Chất liệu</td>
                            <td>Cao cấp, bền bỉ</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Thiết kế</td>
                            <td>Hiện đại, tối ưu hiệu năng</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Hiệu năng</td>
                            <td>Công nghệ mới nhất, hiệu năng cao</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Kết nối</td>
                            <td>Hỗ trợ các chuẩn kết nối phổ biến</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Pin/Nguồn</td>
                            <td>Pin/Nguồn lâu dài, tiết kiệm năng lượng</td>
                        </tr>
                        <tr>
                            <td class="fw-semibold">Bảo hành</td>
                            <td>12 tháng bảo hành chính hãng</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </section>

    <section class="mt-5">
        <h2 class="h5 mb-3">Ưu điểm nổi bật</h2>
        <ul class="list-group">
            <li class="list-group-item"><i class="bi bi-check-circle-fill text-success me-2"></i> Thiết kế hiện đại, gọn nhẹ</li>
            <li class="list-group-item"><i class="bi bi-check-circle-fill text-success me-2"></i> Hiệu năng mạnh mẽ cho công việc và giải trí</li>
            <li class="list-group-item"><i class="bi bi-check-circle-fill text-success me-2"></i> Pin bền, sạc nhanh</li>
            <li class="list-group-item"><i class="bi bi-check-circle-fill text-success me-2"></i> Màn hình sắc nét, độ phân giải cao</li>
            <li class="list-group-item"><i class="bi bi-check-circle-fill text-success me-2"></i> Bảo hành chính hãng, hỗ trợ 24/7</li>
        </ul>
    </section>

    <section class="mt-5">
        <h2 class="h5 mb-3">Hướng dẫn sử dụng</h2>
        <div class="accordion" id="accordionFAQ">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Làm sao để bảo vệ pin tốt nhất?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body">
                        <p>Để bảo vệ pin tốt nhất, bạn nên:</p>
                        <ul>
                            <li>Tránh để pin sạc quá 100% hoặc dưới 20%</li>
                            <li>Không sử dụng thiết bị khi đang sạc pin</li>
                            <li>Tránh nhiệt độ quá cao hoặc quá thấp</li>
                            <li>Cập nhật phần mềm thường xuyên để tối ưu pin</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Thời gian bảo hành là bao lâu?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body">
                        Sản phẩm được bảo hành 12 tháng từ ngày kích hoạt. Bảo hành bao gồm các lỗi kỹ thuật, không bao gồm hư hỏng do sử dụng không đúng.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Có hỗ trợ nâng cấp sau không?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFAQ">
                    <div class="accordion-body">
                        Có, chúng tôi hỗ trợ nâng cấp RAM, lưu trữ để tăng hiệu năng. Liên hệ với bộ phận hỗ trợ để biết chi tiết.
                    </div>
                </div>
            </div>
        </div>
    </section>
    </section>

    {{-- Toast notification container - Center --}}
    <div class="position-fixed top-50 start-50 translate-middle" style="z-index: 11">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 500px;">
            <div class="toast-header bg-success text-white" style="padding: 1.25rem; font-size: 1.2rem;">
                <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i>
                <strong class="me-auto" style="font-size: 1.2rem;">Thành công</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" style="padding: 1.5rem; font-size: 1.1rem;">
                <i class="bi bi-bag-check text-success me-2" style="font-size: 1.3rem;"></i>
                <span id="toastMessage">Thêm vào giỏ hàng thành công!</span>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    // Function để hiển thị toast thông báo (cập nhật tiêu đề theo loại và tự ẩn)
    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('successToast');
        const toastBody = toastElement.querySelector('.toast-body');
        const toastHeader = toastElement.querySelector('.toast-header');
        const titleEl = toastHeader.querySelector('strong.me-auto');

        // Reset header classes
        toastHeader.classList.remove('bg-success', 'bg-danger', 'bg-warning');

        if (type === 'error') {
            toastHeader.classList.add('bg-danger');
            titleEl.textContent = 'Lỗi';
            toastBody.innerHTML = '<i class="bi bi-exclamation-circle text-danger me-2" style="font-size:1.3rem"></i><span>' + message + '</span>';
        } else if (type === 'warning') {
            toastHeader.classList.add('bg-warning');
            titleEl.textContent = 'Chú ý';
            toastBody.innerHTML = '<i class="bi bi-exclamation-triangle text-warning me-2" style="font-size:1.3rem"></i><span>' + message + '</span>';
        } else {
            toastHeader.classList.add('bg-success');
            titleEl.textContent = 'Thành công';
            toastBody.innerHTML = '<i class="bi bi-bag-check text-success me-2" style="font-size:1.3rem"></i><span>' + message + '</span>';
        }

        // Show toast with autohide (4s)
        const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
        toast.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const basePrice = {{ (int)$product->price }};
        const finalPriceInput = document.getElementById('final_price');
        const displayPrice = document.getElementById('display_price');
        const variantSelects = document.querySelectorAll('.variant-select');
        const categoryName = "{{ $product->category ? strtolower($product->category->name) : '' }}";
        
        // Xác định các variant bắt buộc dựa vào category
        let requiredVariants = [];
        if (categoryName === 'laptop') {
            requiredVariants = ['variant_ram', 'variant_ssd', 'variant_color'];
        } else if (categoryName === 'bàn phím') {
            requiredVariants = ['variant_color', 'variant_switch'];
        } else if (categoryName === 'chuột') {
            requiredVariants = ['variant_color'];
        }
        
        function updatePrice() {
            let totalAddition = 0;
            
            // Lấy giá từ tất cả variant select
            variantSelects.forEach(select => {
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption && selectedOption.dataset.price) {
                    totalAddition += parseInt(selectedOption.dataset.price) || 0;
                }
            });
            
            const finalPrice = basePrice + totalAddition;
            
            // Cập nhật hidden input
            finalPriceInput.value = finalPrice;
            
            // Cập nhật display
            displayPrice.textContent = new Intl.NumberFormat('vi-VN').format(finalPrice) + '₫';
        }
        
        // Kiểm tra xem tất cả variant bắt buộc đã được chọn chưa
        function validateVariants() {
            if (requiredVariants.length === 0) return true; // Không có variant bắt buộc
            
            for (let variantName of requiredVariants) {
                const select = document.querySelector(`[name="${variantName}"]`);
                if (!select || select.value === '') {
                    return false;
                }
            }
            return true;
        }
        
        // Lắng nghe sự thay đổi
        variantSelects.forEach(select => {
            select.addEventListener('change', updatePrice);
        });
        
        // Xử lý nút "Thêm vào giỏ hàng"
        document.getElementById('addToCartBtn').addEventListener('click', function(e) {
            if (!validateVariants()) {
                e.preventDefault();
                showToast('Vui lòng chọn đầy đủ các tùy chọn trước khi thêm vào giỏ hàng!', 'warning');
                return false;
            }
            
            // Nếu hợp lệ, submit form bình thường nhưng intercept response
            e.preventDefault();
            
            const form = document.getElementById('addToCartForm');
            const formData = new FormData(form);
            
            // Submit form via fetch để xử lý response
            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                // Ensure cookies/session are sent so Laravel can authenticate
                credentials: 'same-origin'
            })
            .then(async response => {
                // Nếu server trả 401 (Unauthenticated) -> yêu cầu login
                if (response.status === 401) {
                    showToast('Bạn cần đăng nhập để thêm sản phẩm vào giỏ. Chuyển tới trang đăng nhập...', 'warning');
                    setTimeout(() => {
                        window.location.href = "{{ route('login') }}";
                    }, 1400);
                    return null;
                }

                // Cố gắng parse JSON (nhiều trường hợp sẽ là JSON)
                let data = null;
                try {
                    data = await response.json();
                } catch (e) {
                    // Không phải JSON -> hiển thị lỗi chung
                    console.error('Invalid JSON response', e);
                    showToast('Có lỗi xảy ra (phản hồi không hợp lệ).', 'error');
                    return null;
                }

                return data;
            })
            .then(data => {
                if (!data) return;
                if (data.success) {
                    showToast('Sản phẩm đã được thêm vào giỏ hàng thành công!', 'success');
                    // Quay về trang chủ sau 2 giây
                    setTimeout(() => {
                        window.location.href = "{{ route('home') }}";
                    }, 2000);
                } else {
                    showToast(data.message || 'Có lỗi xảy ra!', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
            });
        });
        
        // Xử lý nút "Mua ngay"
        document.getElementById('buyNowBtn').addEventListener('click', function(e) {
            e.preventDefault();
            
            if (!validateVariants()) {
                showToast('Vui lòng chọn đầy đủ các tùy chọn trước khi mua!', 'warning');
                return false;
            }
            
            // Build query parameters cho checkout
            const params = {
                buy_now: 1,
                product_id: {{ $product->id }},
                name: '{{ $product->name }}',
                price: finalPriceInput.value,
                slug: '{{ $product->slug }}',
                qty: 1
            };
            
            // Add variant values
            requiredVariants.forEach(variantName => {
                const select = document.querySelector(`[name="${variantName}"]`);
                if (select) {
                    params[variantName] = select.value;
                }
            });
            
            // Chuyển đến checkout
            const queryString = new URLSearchParams(params).toString();
            window.location.href = "{{ route('checkout.index') }}?" + queryString;
        });
    });
</script>
@endsection