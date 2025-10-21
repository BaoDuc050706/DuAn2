@extends('layouts.app')

@section('title', $category->name)

@section('content')
<h1 class="mb-4">{{ $category->name }}</h1>

@if($products->isNotEmpty())
    <div class="row g-3">
        @foreach($products as $product)
            <div class="col-6 col-md-3">
                <div class="card h-100 product-card">
                    <a href="{{ route('product.show', $product->slug) }}">
                        <img src="{{ $product->image ?? 'https://via.placeholder.com/400x300?text=Product' }}" class="card-img-top" alt="{{ $product->name }}">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <h3 class="h6">
                            <a href="{{ route('product.show', $product->slug) }}" class="text-decoration-none text-dark">{{ $product->name }}</a>
                        </h3>
                        <div class="price mb-2">{{ number_format($product->price,0,',','.') }}₫</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="alert alert-info">Chưa có sản phẩm nào trong danh mục này.</div>
@endif
@endsection
