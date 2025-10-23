@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm')

@section('content')
    <h3>Kết quả tìm kiếm cho: <strong>{{ $query }}</strong></h3>

    @if($products->count())
        <div class="row g-3 mt-3">
            @foreach($products as $product)
                <div class="col-md-3">
                    <div class="product-card p-3 text-center bg-white rounded shadow-sm">
                        <img src="{{ $product->image }}" class="img-fluid mb-2" alt="{{ $product->name }}">
                        <h5>{{ $product->name }}</h5>
                        <p class="price">{{ number_format($product->price) }}₫</p>
                        <a href="{{ route('product.show', $product->slug) }}" class="btn btn-sm btn-dark">Xem chi tiết</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-3 text-muted">Không tìm thấy sản phẩm nào.</p>
    @endif
@endsection
