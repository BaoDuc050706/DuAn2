@extends('layouts.app')

@section('title', $product['name'] . ' - Thông tin sản phẩm')

@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" class="img-fluid rounded shadow-sm">
    </div>
    <div class="col-md-6">
        <h1 class="h3 mb-3">{{ $product['name'] }}</h1>
        <div class="h4 text-danger mb-3">{{ number_format($product['price'], 0, ',', '.') }}₫</div>
        <p class="text-muted">{{ $product['description'] }}</p>

        <div class="d-flex gap-2 mt-4">
            <form method="post" action="#" onsubmit="event.preventDefault(); alert('Đã thêm vào giỏ (demo)');">
                @csrf
                <button class="btn btn-dark">
                    <i class="bi bi-cart-plus"></i> Đặt hàng
                </button>
            </form>

            <a class="btn btn-danger" href="{{ route('checkout.index', [
                'buy_now' => 1,
                'name' => $product['name'],
                'price' => $product['price'],
                'qty' => 1,
            ]) }}">
                Mua ngay
            </a>
        </div>

        <hr class="my-4">
        <div class="small text-muted">
            - Giao nhanh 2h nội thành • Bảo hành chính hãng • Đổi trả 7 ngày
        </div>
    </div>
</div>

<section class="mt-5">
    <h2 class="h5">Mô tả sản phẩm</h2>
    <p>Thiết kế hiện đại, hiệu năng ổn định và bền bỉ. Phù hợp cho cả làm việc và giải trí.</p>
</section>
@endsection


