@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng')
@section('page-title', 'Chi tiết đơn hàng')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Đơn hàng #{{ $order->order_number }}</h5>
                <span class="badge bg-{{ 
                    $order->status === 'delivered' ? 'success' : 
                    ($order->status === 'cancelled' ? 'danger' : 
                    ($order->status === 'shipped' ? 'info' : 'warning')) 
                }} fs-6">
                    @switch($order->status)
                        @case('pending') Chờ xử lý @break
                        @case('processing') Đang xử lý @break
                        @case('shipped') Đã giao @break
                        @case('delivered') Đã nhận @break
                        @case('cancelled') Đã hủy @break
                        @default {{ ucfirst($order->status) }}
                    @endswitch
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Thông tin khách hàng</h6>
                        <ul class="list-unstyled">
                            <li><strong>Tên:</strong> {{ $order->full_name }}</li>
                            <li><strong>Email:</strong> {{ $order->email }}</li>
                            <li><strong>SĐT:</strong> {{ $order->phone }}</li>
                            <li><strong>Địa chỉ:</strong> {{ $order->address }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Thông tin đơn hàng</h6>
                        <ul class="list-unstyled">
                            <li><strong>Mã đơn:</strong> {{ $order->order_number }}</li>
                            <li><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</li>
                            <li><strong>Phương thức TT:</strong> 
                                @switch($order->payment_method)
                                    @case('cod') Thanh toán khi nhận hàng @break
                                    @case('bank') Chuyển khoản ngân hàng @break
                                    @case('card') Thẻ tín dụng @break
                                    @default {{ ucfirst($order->payment_method) }}
                                @endswitch
                            </li>
                        </ul>
                    </div>
                </div>

                <h6>Sản phẩm đã đặt</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item['name'] }}</td>
                                <td>{{ $item['qty'] }}</td>
                                <td>{{ number_format($item['price'], 0, ',', '.') }} VNĐ</td>
                                <td>{{ number_format($item['qty'] * $item['price'], 0, ',', '.') }} VNĐ</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Tạm tính:</strong></td>
                                <td><strong>{{ number_format($order->subtotal, 0, ',', '.') }} VNĐ</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3"><strong>Phí vận chuyển:</strong></td>
                                <td><strong>{{ number_format($order->shipping, 0, ',', '.') }} VNĐ</strong></td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="3"><strong>Tổng cộng:</strong></td>
                                <td><strong>{{ number_format($order->total, 0, ',', '.') }} VNĐ</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Cập nhật trạng thái</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Trạng thái mới</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Đã giao</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Đã nhận</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Cập nhật trạng thái
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6>Thao tác nhanh</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.orders') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại danh sách
                    </a>
                    
                    @if($order->status !== 'delivered' && $order->status !== 'cancelled')
                    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" 
                          onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?')">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="fas fa-times"></i> Hủy đơn hàng
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
