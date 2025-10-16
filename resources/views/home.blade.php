@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')

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
@if(!empty($categories) && ($categories instanceof \Illuminate\Support\Collection ? $categories->isNotEmpty() : count($categories) > 0))
<section class="mb-4">
    <h2 class="h5 mb-3 section-title">Danh mục nổi bật</h2>
    <div class="row g-3">
        @foreach($categories as $cat)
            <div class="col-6 col-md-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="fw-semibold mb-2">{{ $cat->name }}</div>
                        <a href="#" class="btn btn-sm btn-outline-dark">Xem sản phẩm</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif

{{-- Featured products --}}
<section class="mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0 section-title">Sản phẩm nổi bật</h2>
        <div class="text-muted small">Hiển thị {{ $products instanceof \Illuminate\Support\Collection ? $products->count() : (is_countable($products) ? count($products) : 0) }} sản phẩm</div>
    </div>
    <div class="row g-3">
        @forelse($products as $product)
            <div class="col-6 col-md-3">
                <div class="card h-100 product-card">
                    <a href="{{ route('product.show', $product->slug) }}">
                        <img src="{{ $product->image ?? 'https://via.placeholder.com/400x300?text=Product' }}" class="card-img-top" alt="{{ $product->name }}">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6"><a class="text-decoration-none text-dark" href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                        @if(isset($product->discount) && $product->discount > 0)
                        <div class="price mb-2">
                            <span class="text-muted text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                            <span class="ms-2 text-danger fw-semibold">{{ number_format($product->price * (1 - $product->discount/100), 0, ',', '.') }}₫</span>
                        </div>
                        @else
                        <div class="price mb-2">{{ number_format($product->price, 0, ',', '.') }}₫</div>
                        @endif
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
        @empty
            <div class="col-12">
                <div class="alert alert-info">Chưa có sản phẩm nào.</div>
            </div>
        @endforelse
    </div>
</section>
@endsection

