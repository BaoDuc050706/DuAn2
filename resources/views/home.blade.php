@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
@php
use Illuminate\Support\Str;
@endphp

{{-- Hero section --}}
<section class="hero p-5 mb-4 rounded">
    <div class="container-fluid py-5">
        <span class="badge">
            <a href="#hot-deal" class="text-decoration-none text-white">HOT SALE</a>
        </span>
        <h1 class="display-6 fw-bold mt-2">Nâng tầm trải nghiệm gaming cùng CAEKT</h1>
        <p class="col-md-8 fs-5 text-white-50">Ưu đãi phụ kiện gaming chính hãng. Giao nhanh, bảo hành 1 đổi 1.</p>
        <div class="d-flex gap-2">
            <a href="#featured-products" class="btn btn-danger btn-lg">
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
            <div class="card h-100 categories-card">
                <div class="card-body text-center">
                    <div class="fw-semibold mb-2">{{ $cat->name }}</div>
                    <a href="{{ route('category.show', $cat->slug) }}" class="btn btn-sm categories-btn">Xem sản phẩm</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</section>
@endif



{{-- Featured products --}}
<section class="mb-4" id="featured-products">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0 section-title">
            @if(isset($activeCategory))
            @if(request('show_all'))
            Tất cả sản phẩm {{ $activeCategory->name }}
            @else
            Sản phẩm {{ $activeCategory->name }}
            @endif
            @else
            @if(request('show_all'))
            Tất cả sản phẩm nổi bật
            @else
            Sản phẩm nổi bật
            @endif
            @endif
        </h2>
        <div class="d-flex align-items-center gap-3">
            @if(request('show_all'))
            <a href="{{ route('home', request()->except('show_all')) }}" class="text-decoration-none show-all-link">
                <div class="text-muted small">
                    <i class="bi bi-arrow-left me-1"></i>
                    Thu gọn hiển thị
                </div>
            </a>
            @else
            <a href="{{ route('home', array_merge(request()->query(), ['show_all' => '1'])) }}" class="text-decoration-none show-all-link">
                <div class="text-muted small">
                    @if(isset($activeCategory))
                    Hiển thị tất cả sản phẩm {{ $activeCategory->name }}
                    @else
                    Hiển thị tất cả sản phẩm
                    @endif
                    <i class="bi bi-arrow-right ms-1"></i>
                </div>
            </a>
            @endif
            @if(isset($activeCategory))
            <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-house"></i> Về trang chủ
            </a>
            @endif
        </div>
    </div>
    <div class="row g-3">
        @forelse($products as $product)
        <div class="col-6 col-md-3">
            <div class="card h-100 product-card position-relative">
                <a href="{{ route('product.show', $product->slug) }}">
                    @php
                    $img = $product->image;
                    if ($img && !Str::startsWith($img, ['http://', 'https://', '/'])) {
                    $img = asset('image/' . $img);
                    }
                    @endphp
                    <img src="{{ $img ?: 'https://via.placeholder.com/400x300?text=Product' }}" class="card-img-top" alt="{{ $product->name }}">
                </a>
                
                {{-- Badge giảm giá --}}
                @if(isset($product->discount) && $product->discount > 0)
                <div class="position-absolute top-0 end-0 m-2">
                    <span class="badge bg-danger" style="font-size: 0.9rem; padding: 0.5rem 0.75rem;">
                        <i class="bi bi-star-fill"></i> -{{ $product->discount }}%
                    </span>
                </div>
                @endif
                
                <div class="card-body d-flex flex-column">
                    <h3 class="h6"><a class="text-decoration-none text-dark"
                            href="{{ route('product.show', $product->slug) }}">{{ $product->name }}</a></h3>
                    @if(isset($product->discount) && $product->discount > 0)
                    <div class="price mb-2">
                        <span
                            class="text-muted text-decoration-line-through">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                        <span
                            class="ms-2 text-danger fw-semibold">{{ number_format($product->price * (1 - $product->discount / 100), 0, ',', '.') }}₫</span>
                    </div>
                    @else
                    <div class="price mb-2">{{ number_format($product->price, 0, ',', '.') }}₫</div>
                    @endif
                    <div class="d-flex gap-2 mt-auto">
                        <a class="btn btn-danger w-100" href="{{ route('product.show', $product->slug) }}">
                            <i class="bi bi-lightning-fill"></i> Mua ngay
                        </a>
                    </div>
                    <div class="mt-2 text-center small text-muted">
                        <i class="bi bi-bag-check"></i> 
                        {{ isset($product->sold) ? number_format($product->sold) : rand(50, 500) }} sản phẩm đã bán
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