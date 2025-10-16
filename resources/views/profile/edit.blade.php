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

@extends('layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Thông tin cá nhân</h1>

                @php $readOnly = ($mode ?? null) !== 'edit'; @endphp
                @if($readOnly)
                    <div class="mb-3">
                        <a href="{{ route('profile.edit', ['mode' => 'edit']) }}" class="btn btn-primary">Sửa thông tin</a>
                    </div>
                @endif

                <form method="post" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PUT')
                    @if (!empty($redirect))
                        <input type="hidden" name="redirect" value="{{ $redirect }}">
                    @endif
                    <div class="mb-3">
                        <label class="form-label" for="name">Họ và tên</label>
                        <input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" {{ $readOnly ? 'readonly' : '' }} required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="city">Tỉnh/Thành phố</label>
                            <input class="form-control" id="city" name="city" value="{{ old('city', $user->city) }}" {{ $readOnly ? 'readonly' : '' }} required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="district">Quận/Huyện</label>
                            <input class="form-control" id="district" name="district" value="{{ old('district', $user->district) }}" {{ $readOnly ? 'readonly' : '' }} required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ward">Phường/Xã</label>
                            <input class="form-control" id="ward" name="ward" value="{{ old('ward', $user->ward) }}" {{ $readOnly ? 'readonly' : '' }} required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="address_line">Địa chỉ chi tiết</label>
                            <input class="form-control" id="address_line" name="address_line" value="{{ old('address_line', $user->address_line) }}" {{ $readOnly ? 'readonly' : '' }} required>
                        </div>
                    </div>
                    @if(!$readOnly)
                        <button class="btn btn-dark" type="submit">Lưu thay đổi</button>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">Hủy</a>
                    @endif
                </form>
            </div>
        </div>
    </div>
    
</div>
@endsection


