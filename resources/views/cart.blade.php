@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<h1 class="h4 mb-3">Giỏ hàng</h1>
<div class="card">
    <div class="card-body">
        @if (empty($cartItems))
            <div class="text-center text-muted py-4">Giỏ hàng của bạn đang trống.</div>
        @endif
        @foreach($cartItems as $index => $item)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div>{{ $item['name'] }}</div>
                    <div class="btn-group" role="group">
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
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="fw-bold">{{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}₫</div>
                    <form method="post" action="{{ route('cart.remove', $index) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger">Xóa</button>
                    </form>
                </div>
            </div>
        @endforeach

        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('checkout.index') }}" class="btn btn-danger {{ empty($cartItems) ? 'disabled' : '' }}" {{ empty($cartItems) ? 'aria-disabled=true tabindex=-1' : '' }}>Thanh toán</a>
        </div>
    </div>
</div>
@endsection


