@extends('layouts.app')

@section('title', 'Tra cứu đơn hàng')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-3">Tra cứu đơn hàng</h1>

    <form class="mb-4" method="get" action="{{ route('orders.lookup') }}">
        <div class="input-group">
            <input type="text" name="code" class="form-control" placeholder="Nhập mã đơn (ví dụ: 169...)" value="{{ old('code', $code ?? '') }}">
            <button class="btn btn-primary" type="submit">Tra cứu</button>
        </div>
    </form>

    @if(isset($code) && $code !== '')
        @if($order)
            <div class="card">
                <div class="card-body">
                    <h5>Mã đơn: {{ $order['id'] }}</h5>
                    <p><strong>Ngày:</strong> {{ $order['created_at'] }}</p>
                    <p><strong>Tổng:</strong> {{ number_format($order['total'] ?? 0, 0, ',', '.') }}₫</p>
                    <hr>
                    <h6>Sản phẩm</h6>
                    <ul>
                        @foreach($order['items'] as $item)
                            <li>{{ $item['name'] }} x{{ $item['qty'] }} — {{ number_format($item['price'], 0, ',', '.') }}₫</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @else
            <div class="alert alert-warning">Không tìm thấy đơn hàng với mã '{{ $code }}'.</div>
        @endif
    @endif
</div>
@endsection
