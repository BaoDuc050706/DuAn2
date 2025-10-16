@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
@php
    $categories = [
        ['label' => 'Tai nghe'],
        ['label' => 'Chuột'],
        ['label' => 'Bàn phím'],
        ['label' => 'Màn hình'],
    ];

    $products = [
        [
            'slug' => 'tai-nghe-gaming-x1',
            'name' => 'Tai nghe Gaming X1',
            'price' => 1299000,
            'image' => 'https://images.unsplash.com/photo-1518445282155-7950073c585e?q=80&w=800&auto=format&fit=crop',
        ],
        [
            'slug' => 'chuot-khong-day-pro',
            'name' => 'Chuột không dây Pro',
            'price' => 799000,
            'image' => 'https://images.unsplash.com/photo-1587820650444-3c6691f92b9f?q=80&w=800&auto=format&fit=crop',
        ],
        [
            'slug' => 'ban-phim-co-rgb',
            'name' => 'Bàn phím cơ RGB',
            'price' => 1599000,
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=800&auto=format&fit=crop',
        ],
        [
            'slug' => 'man-hinh-27-144hz',
            'name' => 'Màn hình 27" 144Hz',
            'price' => 4999000,
            'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=800&auto=format&fit=crop',
        ],
    ];
@endphp

{{-- Hero section --}}
<section class="hero p-5 mb-4 rounded">
    <div class="container-fluid py-5">
        <span class="badge">HOT SALE</span>
        <h1 class="display-6 fw-bold mt-2">Nâng tầm trải nghiệm gaming cùng GearZone</h1>
        <p class="col-md-8 fs-5 text-white-50">Ưu đãi phụ kiện gaming chính hãng. Giao nhanh, bảo hành 1 đổi 1.</p>
        <div class="d-flex gap-2">
            <a href="/products" class="btn btn-danger btn-lg">
                <i class="bi bi-lightning-fill"></i> Mua ngay
            </a>
            <a href="{{ route('checkout.index') }}" class="btn btn-outline-light btn-lg">
                <i class="bi bi-bag-check"></i> Thanh toán
            </a>
        </div>
    </div>
</section>

{{-- Categories --}}
<section class="mb-4">
    <h2 class="h5 mb-3 section-title">Danh mục nổi bật</h2>
    <div class="row g-3">
        @foreach($categories as $cat)
            <div class="col-6 col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="fw-semibold mb-2">{{ $cat['label'] }}</div>
                        <a href="#" class="btn btn-sm btn-outline-dark">Xem sản phẩm</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- Featured products --}}
<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0 section-title">Sản phẩm nổi bật</h2>
        <div class="text-muted small">Hiển thị {{ count($products) }} sản phẩm</div>
    </div>
    <div class="row g-3">
        @foreach($products as $product)
            <div class="col-6 col-md-3">
                <div class="card h-100 product-card">
                    <a href="{{ route('product.show', $product['slug']) }}">
                        <img src="{{ $product['image'] }}" class="card-img-top" alt="{{ $product['name'] }}">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6"><a class="text-decoration-none text-dark" href="{{ route('product.show', $product['slug']) }}">{{ $product['name'] }}</a></h3>
                        <div class="price mb-2">{{ number_format($product['price'], 0, ',', '.') }}₫</div>
                        <div class="d-flex gap-2 mt-auto">
                            <form method="post" action="{{ route('cart.add') }}" class="add-to-cart-form">
                                @csrf
                                <input type="hidden" name="name" value="{{ $product['name'] }}">
                                <input type="hidden" name="price" value="{{ $product['price'] }}">
                                <input type="hidden" name="qty" value="1">
                                <input type="hidden" name="slug" value="{{ $product['slug'] }}">
                                <button class="btn btn-outline-dark" type="submit">Thêm vào giỏ hàng</button>
                            </form>
                            <a class="btn btn-danger" href="{{ route('checkout.index', ['buy_now' => 1, 'name' => $product['name'], 'price' => $product['price'], 'qty' => 1]) }}">Mua ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection
