@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="row g-4">
    <div class="col-lg-7">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header bg-dark text-white">Thông tin giao hàng</div>
            <div class="card-body">
                <form method="POST" action="{{ route('checkout.process') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}">
                        @error('full_name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea name="address" rows="3" class="form-control">{{ old('address') }}</textarea>
                        @error('address')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phương thức thanh toán</label>
                        <select name="payment_method" class="form-select">
                            <option value="cod" {{ old('payment_method') === 'cod' ? 'selected' : '' }}>Thanh toán khi nhận hàng (COD)</option>
                            <option value="bank" {{ old('payment_method') === 'bank' ? 'selected' : '' }}>Chuyển khoản ngân hàng</option>
                            <option value="card" {{ old('payment_method') === 'card' ? 'selected' : '' }}>Thẻ tín dụng/ghi nợ</option>
                        </select>
                        @error('payment_method')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
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
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-semibold">{{ $item['name'] }}</div>
                                <div class="text-muted small">x{{ $item['qty'] }}</div>
                            </div>
                            <div class="fw-bold">{{ number_format($item['qty'] * $item['price'], 0, ',', '.') }}₫</div>
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


