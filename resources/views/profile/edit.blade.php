@extends('layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <span>Thông tin cá nhân</span>
                @if(!isset($readOnly) || $readOnly)
                    <a href="{{ route('profile.edit', ['mode' => 'edit']) }}" class="btn btn-sm btn-light">Sửa thông tin</a>
                @endif
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" {{ (!isset($readOnly) || $readOnly) ? 'readonly' : '' }}>
                        @error('name')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Gmail</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" {{ (!isset($readOnly) || $readOnly) ? 'readonly' : '' }}>
                        @error('email')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" {{ (!isset($readOnly) || $readOnly) ? 'readonly' : '' }}>
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}" {{ (!isset($readOnly) || $readOnly) ? 'readonly' : '' }}>
                            @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    @if(!isset($readOnly) || !$readOnly)
                        <button type="submit" class="btn btn-danger">Lưu thay đổi</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection