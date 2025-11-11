{{-- View: Hiển thị kết quả tra cứu đơn hàng và sản phẩm đã thanh toán --}}

@extends('layouts.app')

@section('title', 'Kết quả tra cứu đơn hàng')

@section('content')
<div class="container py-4">
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(isset($order) && $order)
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-1">Mã đơn: {{ $order->order_code ?? $order->id }}</h5>
                        <small class="text-muted">Ngày: {{ optional($order->created_at)->format ? $order->created_at->format('d/m/Y H:i') : ($order->created_at ?? '') }}</small>
                    </div>
                    <div class="text-end">
                        <div><strong>Tổng:</strong> {{ number_format($order->total ?? $order->grand_total ?? 0, 0, ',', '.') }}₫</div>
                        @if(isset($order->status))<div><small class="text-muted">Trạng thái: {{ $order->status }}</small></div>@endif
                    </div>
                </div>

                <hr>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6>Người nhận</h6>
                        <p class="mb-1">{{ $order->user->name ?? $order->full_name ?? $order->name ?? '—' }}</p>
                        <p class="mb-1">{{ $order->phone ?? $order->user->phone ?? '—' }}</p>
                        <p class="mb-1">{{ $order->email ?? $order->user->email ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Địa chỉ</h6>
                        <p class="mb-1">{{ $order->address ?? $order->shipping_address ?? '—' }}</p>
                    </div>
                </div>

                <h6>Sản phẩm đã mua</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th style="width:80px">Ảnh</th>
                                <th>Sản phẩm</th>
                                <th style="width:120px">Giá</th>
                                <th style="width:100px">Số lượng</th>
                                <th style="width:140px">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->orderDetails as $detail)
                                <tr>
                                    <td>
                                        @php
                                            $product = $detail->product ?? null;
                                            $imgUrl = null;
                                            if ($product) {
                                                // common field name 'image' used in this project
                                                if (!empty($product->image)) {
                                                    $imgUrl = asset('image/' . $product->image);
                                                }
                                                // try other common fallbacks
                                                if (!$imgUrl && !empty($product->thumbnail)) {
                                                    $imgUrl = asset('image/' . $product->thumbnail);
                                                }
                                            }
                                        @endphp
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $product->name ?? '' }}" class="img-fluid" style="height:60px; object-fit:cover">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center" style="height:60px;">—</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div><strong>{{ $product->name ?? $detail->product_name ?? $detail->name ?? 'Sản phẩm' }}</strong></div>
                                        @if(!empty($detail->options))
                                            <small class="text-muted">{{ is_array($detail->options) ? implode(', ', $detail->options) : $detail->options }}</small>
                                        @endif
                                    </td>
                                    <td>{{ number_format($detail->price ?? $detail->unit_price ?? 0, 0, ',', '.') }}₫</td>
                                    <td>{{ $detail->qty ?? $detail->quantity ?? 1 }}</td>
                                    <td>{{ number_format( (($detail->price ?? $detail->unit_price ?? 0) * ($detail->qty ?? $detail->quantity ?? 1)), 0, ',', '.') }}₫</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Không có sản phẩm trong đơn hàng này.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a href="{{ url()->previous() ?: route('home') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">Không tìm thấy đơn hàng hoặc dữ liệu chưa được cung cấp.</div>
    @endif

</div>