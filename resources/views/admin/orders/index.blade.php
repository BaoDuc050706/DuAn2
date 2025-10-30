@extends('admin.layouts.app')

@section('title', 'Quản lý đơn hàng')
@section('page-title', 'Quản lý đơn hàng')

@section('content')
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col">
                <h5 class="mb-0">Danh sách đơn hàng</h5>
            </div>
            <div class="col-auto">
                <form method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã nhận</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Tìm theo mã đơn, tên, email..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                        </td>
                        <td>{{ $order->full_name }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ number_format($order->total, 0, ',', '.') }} VNĐ</td>
                        <td>
                            <span class="badge bg-{{ 
                                $order->status === 'delivered' ? 'success' : 
                                ($order->status === 'cancelled' ? 'danger' : 
                                ($order->status === 'shipped' ? 'info' : 'warning')) 
                            }}">
                                @switch($order->status)
                                @case('pending') Chờ xử lý @break
                                @case('processing') Đang xử lý @break
                                @case('shipped') Đã giao @break
                                @case('delivered') Đã nhận @break
                                @case('cancelled') Đã hủy @break
                                @default {{ ucfirst($order->status) }}
                                @endswitch
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-cog"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}"
                                                style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="processing">
                                                <button type="submit" class="dropdown-item">Đang xử lý</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}"
                                                style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="shipped">
                                                <button type="submit" class="dropdown-item">Đã giao</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}"
                                                style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="delivered">
                                                <button type="submit" class="dropdown-item">Đã nhận</button>
                                            </form>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form method="POST" action="{{ route('admin.orders.update-status', $order) }}"
                                                style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="dropdown-item text-danger">Hủy đơn</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i><br>
                            Chưa có đơn hàng nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
@endsection