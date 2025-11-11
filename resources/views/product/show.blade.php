@extends('layouts.app')

@section('title', $product->name . ' - Thông tin sản phẩm')

@section('content')
    <div class="row g-4">
        <div class="col-md-6">
 <img src="{{ asset('image/' . $product->image) }}" 
     alt="{{ $product->name }}" 
     class="img-fluid">



        </div>
        <div class="col-md-6">
            <h1 class="h3 mb-3">{{ $product->name }}</h1>
            <div class="h4 text-danger mb-3">{{ number_format($product->price, 0, ',', '.') }}₫</div>
            <p class="text-muted">{{ $product->description }}</p>

            <div class="d-flex gap-2 mt-4">
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


                <a class="btn btn-danger" href="{{ route('checkout.index', [
        'buy_now' => 1,
        'name' => $product->name,
        'price' => $product->price,
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