@extends('admin.layouts.app')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Sản phẩm</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm sản phẩm
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Hình ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Danh mục</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>
                            @if($product->image)
                            <img src="{{ asset('image/' . $product->image) }}" alt="{{ $product->name }}"
                                style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                            @else
                            <div class="bg-light d-flex align-items-center justify-content-center"
                                style="width: 50px; height: 50px;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                            @endif
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ number_format($product->price, 0, ',', '.') }} VNĐ</td>
                        <td>{{ $product->category->name ?? 'Chưa phân loại' }}</td>
                        <td>
                            <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }}">
                                {{ $product->stock > 0 ? 'Còn hàng' : 'Hết hàng' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                    style="display: inline;" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                            Chưa có sản phẩm nào
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($products->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
@endsection