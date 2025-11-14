@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        

        <div class="card">
            <div class="card-header bg-dark text-white">Thông tin giao hàng</div>
            <div class="card-body">
                <form method="POST" action="{{ route('checkout.process') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name', auth()->check() ? auth()->user()->name : '') }}">
                        @error('full_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}">
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', auth()->check() ? auth()->user()->phone : '') }}">
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="address" rows="3" class="form-control">{{ old('address', auth()->check() ? auth()->user()->address : '') }}</textarea>
                        @error('address')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phương thức thanh toán</label>
                        <select name="payment_method" id="payment_method" class="form-select">
                            <option value="cod" {{ old('payment_method') === 'cod' ? 'selected' : '' }}>Thanh toán khi nhận hàng (COD)</option>
                            <option value="bank" {{ old('payment_method') === 'bank' ? 'selected' : '' }}>Chuyển khoản ngân hàng</option>
                            <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Thẻ tín dụng/ghi nợ</option>
                        </select>
                        @error('payment_method')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- QR Code Section - Hiển thị khi chọn thanh toán ngân hàng -->
                    <div id="qr_container" class="mb-3" style="display: none;">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">Mã QR Chuyển Khoản Ngân Hàng</h6>
                            <p class="mb-3">Vui lòng quét mã QR để thực hiện chuyển khoản:</p>
                            
                            <div class="text-center mb-3">
                                @if(isset($fakeQrData) && !empty($fakeQrData))
                                    {!! QrCode::size(250)->generate($fakeQrData) !!}
                                @else
                                    {!! QrCode::size(250)->generate('Ngan hang ABC - STK 123456789') !!}
                                @endif
                            </div>

                            <p class="mb-2"><small><strong>Nội dung chuyển:</strong> Ngân hàng ABC - STK 123456789</small></p>
                            <p class="mb-0"><small><strong>Số tiền:</strong> {{ isset($total) ? number_format($total) : '0' }}₫</small></p>
                            <p class="text-muted mt-2 mb-0"><small>Dùng app ngân hàng để quét QR và hoàn tất thanh toán</small></p>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-danger px-4">Đặt hàng</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header bg-dark text-white">Đơn hàng của bạn</div>
            <div class="card-body">
                <ul class="list-group mb-3">
                    @foreach($cartItems as $item)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-semibold fs-6">{{ $item['name'] }}</div>
                                    <div class="text-muted">{{ number_format($item['price'], 0, ',', '.') }}₫ / sp × {{ $item['qty'] }}</div>
                                    
                                    {{-- Hiển thị các variant nếu có --}}
                                    @if(!empty($item['variant_ram']) || !empty($item['variant_ssd']) || !empty($item['variant_color']) || !empty($item['variant_switch']))
                                        <div class="text-secondary mt-2">
                                            <strong>Chi tiết:</strong>
                                            @if(!empty($item['variant_ram']))
                                                <span class="badge bg-light text-dark">{{ ucfirst($item['variant_ram']) }}</span>
                                            @endif
                                            @if(!empty($item['variant_ssd']))
                                                <span class="badge bg-light text-dark">{{ ucfirst($item['variant_ssd']) }}</span>
                                            @endif
                                            @if(!empty($item['variant_color']))
                                                <span class="badge bg-light text-dark">Màu: {{ ucfirst(str_replace('_', ' ', $item['variant_color'])) }}</span>
                                            @endif
                                            @if(!empty($item['variant_switch']))
                                                <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $item['variant_switch'])) }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="fw-bold fs-6">
                                    {{ number_format($item['qty'] * $item['price'], 0, ',', '.') }}₫
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
                <div class="d-flex justify-content-between mb-1">
                    <span>Tạm tính</span>
                    <span>{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Phí vận chuyển</span>
                    <span>{{ $shipping === 0 ? 'Miễn phí' : number_format($shipping, 0, ',', '.') . '₫' }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fs-5">
                    <span>Tổng cộng</span>
                    <span class="fw-bold text-danger">{{ number_format($total, 0, ',', '.') }}₫</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Đảm bảo DOM đã load hoàn toàn
    function initPaymentMethodListener() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const qrContainer = document.getElementById('qr_container');
        
        if (!paymentMethodSelect || !qrContainer) {
            console.error('Không tìm thấy các phần tử');
            return;
        }
        
        function updateQRDisplay() {
            console.log('Payment method:', paymentMethodSelect.value);
            if (paymentMethodSelect.value === 'bank') {
                qrContainer.style.display = 'block';
                console.log('Hiển thị QR');
            } else {
                qrContainer.style.display = 'none';
                console.log('Ẩn QR');
            }
        }
        
        // Lắng nghe sự thay đổi
        paymentMethodSelect.addEventListener('change', updateQRDisplay);
        
        // Khởi tạo trạng thái ban đầu
        updateQRDisplay();
    }
    
    // Chạy khi DOM sẵn sàng
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPaymentMethodListener);
    } else {
        initPaymentMethodListener();
    }
</script>
@endsection


