@extends('layouts.app')

@section('title', $category->name)

@section('content')
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    {{-- Category Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">{{ $category->name }}</h1>
        <div class="text-muted small">Hiển thị {{ $products->count() }} sản phẩm</div>
    </div>

@if($products->isNotEmpty())
    <div class="row g-3">
        @foreach($products as $product)
            <div class="col-6 col-md-3">
                <div class="card h-100 product-card">
                    <a href="{{ route('product.show', $product->slug) }}">
                        <img src="{{ asset($product->image) ?? 'https://via.placeholder.com/400x300?text=Product' }}" class="card-img-top" alt="{{ $product->name }}">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6">
                            <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark">{{ $product->name }}</a>
                        </h3>
                        @if(isset($product->discount) && $product->discount > 0)
                            <div class="price mb-2">
                                <span class="text-muted text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                                <span class="ms-2 text-danger fw-semibold">{{ number_format($product->price * (1 - $product->discount / 100), 0, ',', '.') }}₫</span>
                            </div>
                        @else
                            <div class="price mb-2">{{ number_format($product->price,0,',','.') }}₫</div>
                        @endif
                        <div class="d-flex gap-2 mt-auto">
                            <form action="{{ route('cart.add') }}" method="POST">
    @csrf
    <input type="hidden" name="product_id" value="{{ $product->id }}">
    <input type="hidden" name="name" value="{{ $product->name }}">
    <input type="hidden" name="price" value="{{ $product->price }}">
    <input type="hidden" name="qty" value="1">
    <input type="hidden" name="slug" value="{{ $product->slug }}">
    <button class="btn btn-dark" type="submit">
        <i class="bi bi-cart-plus"></i> Thêm vào giỏ hàng
    </button>
</form>

                            <a class="btn btn-danger"
                                href="{{ route('checkout.index', ['buy_now' => 1, 'name' => $product->name, 'price' => $product->price, 'qty' => 1]) }}">Mua
                                ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-info">Chưa có sản phẩm nào trong danh mục này.</div>
@endif
@endsection
