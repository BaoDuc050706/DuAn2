@extends('admin.layouts.app')

@section('title', 'Sửa sản phẩm')
@section('page-title', 'Sửa sản phẩm')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5>Chỉnh sửa sản phẩm: {{ $product->name }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="name" name="name" value="{{ old('name', $product->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Giá <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror"
                            id="price" name="price" value="{{ old('price', $product->price) }}" min="0" step="1000" required>
                        @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục</label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                            id="category_id" name="category_id">
                            <option value="">Chọn danh mục</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="discount" class="form-label">Giảm giá (%)</label>
                        <input type="number" class="form-control @error('discount') is-invalid @enderror"
                            id="discount" name="discount" value="{{ old('discount', $product->discount) }}"
                            min="0" max="100" step="1">
                        @error('discount')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="stock" class="form-label">Số lượng tồn kho</label>
                        <input type="number" class="form-control @error('stock') is-invalid @enderror"
                            id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                        @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        @if($product->image)
                        <div class="mb-2">
                            <img src="{{ asset('image/' . $product->image) }}" alt="{{ $product->name }}"
                                style="width: 100px; height: 100px; object-fit: cover;" class="rounded">
                            <div class="form-text">Hình ảnh hiện tại</div>
                        </div>
                        @endif
                        <input type="file" class="form-control @error('image') is-invalid @enderror"
                            id="image" name="image" accept="image/*">
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Chọn file ảnh mới để thay thế (JPG, PNG, GIF) - tối đa 2MB</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập nhật sản phẩm
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6>Thông tin sản phẩm</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled small">
                    <li><strong>ID:</strong> {{ $product->id }}</li>
                    <li><strong>Slug:</strong> {{ $product->slug }}</li>
                    <li><strong>Ngày tạo:</strong> {{ $product->created_at->format('d/m/Y H:i') }}</li>
                    <li><strong>Cập nhật cuối:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection