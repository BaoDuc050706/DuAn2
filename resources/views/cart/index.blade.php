@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container my-4">
    <h1 class="h4 mb-3">Giỏ hàng</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            {{-- Nếu giỏ hàng trống --}}
            @if (empty($cartItems))
                <div class="text-center text-muted py-5">
                    <i class="bi bi-cart-x display-6 d-block mb-2"></i>
                    Giỏ hàng của bạn đang trống.
                    <div class="mt-3">
                        <a href="{{ route('home') }}" class="btn btn-dark">Tiếp tục mua sắm</a>
                    </div>
                </div>
            @else
                {{-- Danh sách sản phẩm --}}
                @foreach($cartItems as $index => $item)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $item['name'] }}</div>
                            <div class="small text-muted">{{ number_format($item['price'], 0, ',', '.') }}₫ / sp</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <form method="post" action="{{ route('cart.dec', $index) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-secondary">-</button>
                            </form>

                            <span class="px-2">{{ $item['qty'] }}</span>

                            <form method="post" action="{{ route('cart.inc', $index) }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-secondary">+</button>
                            </form>

                            <div class="fw-bold ms-3">
                                {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}₫
                            </div>

                            <form method="post" action="{{ route('cart.remove', $index) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger ms-2">Xóa</button>
                            </form>
                        </div>
                    </div>
                @endforeach

                {{-- Tổng cộng --}}
                <div class="mt-4 border-top pt-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="fw-bold fs-5">Tổng cộng:</div>
                        <div class="h5 mb-0 text-danger">{{ number_format($total, 0, ',', '.') }}₫</div>
                    </div>
                </div>

                {{-- Nút hành động --}}
                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('checkout.index') }}" class="btn btn-danger">Thanh toán</a>
                    <form method="post" action="{{ route('cart.clear') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">Xóa giỏ hàng</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
