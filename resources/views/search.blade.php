@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm')

@section('content')
    <h3>Kết quả tìm kiếm cho: <strong>{{ $query }}</strong></h3>

    @if($products->count())
        <div class="row g-3 mt-3">
            @foreach($products as $product)
                <div class="col-6 col-md-3">
                    <div class="card h-100 product-card">
                        <a href="{{ route('product.show', $product->slug) }}">
                            @php
                                $imgRaw = $product->image;
                                $img = null;

                                // If image is an absolute URL, use it as-is
                                if ($imgRaw && \Illuminate\Support\Str::startsWith($imgRaw, ['http://', 'https://'])) {
                                    $img = $imgRaw;
                                }
                                // If image looks like an absolute path on the server (starts with '/'), make it asset-safe
                                elseif ($imgRaw && \Illuminate\Support\Str::startsWith($imgRaw, ['/'])) {
                                    $img = asset(ltrim($imgRaw, '/'));
                                }
                                // Otherwise treat as a filename stored in public/image/ — urlencode the filename to handle spaces/special chars
                                elseif ($imgRaw) {
                                    $img = asset('image/' . rawurlencode($imgRaw));
                                }
                            @endphp
                            <img src="{{ $img ?: 'https://via.placeholder.com/400x300?text=Product' }}" class="card-img-top"
                                alt="{{ $product->name }}">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h3 class="h6">
                                <a href="{{ route('product.show', $product->slug) }}"
                                    class="text-decoration-none text-dark">{{ $product->name }}</a>
                            </h3>
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
                                    <i class="bi bi-lightning-fill"></i> Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-3 text-muted">Không tìm thấy sản phẩm nào.</p>
    @endif
@endsection
