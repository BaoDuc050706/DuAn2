@extends('layouts.app')

@section('title', 'Lịch sử đơn hàng')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">Lịch sử mua hàng</h1>

    @if($orders->isEmpty())
        <div class="alert alert-info">Bạn chưa có đơn hàng nào.</div>
    @else
        <div class="list-group">
            @foreach($orders as $order)
                <div class="list-group-item mb-2">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong>Mã đơn:</strong> {{ $order['id'] }}<br>
                            <strong>Ngày:</strong> {{ $order['created_at'] }}
                        </div>
                        <div class="text-end">
                            <strong>Tổng:</strong> {{ number_format($order['total'] ?? 0, 0, ',', '.') }}₫
                        </div>
                    </div>
                    <hr>
                    <div>
                        <strong>Người nhận:</strong> {{ $order['full_name'] }} — {{ $order['phone'] }}<br>
                        <strong>Địa chỉ:</strong> {{ $order['address'] }}
                    </div>
                    <hr>
                    <div>
                        <strong>Sản phẩm:</strong>
                        <ul>
                            @foreach($order['items'] as $item)
                                <li>{{ $item['name'] }} x{{ $item['qty'] }} — {{ number_format($item['price'], 0, ',', '.') }}₫</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
