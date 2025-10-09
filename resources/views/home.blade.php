@extends('layouts.app')

@section('content')
    <div class="flex flex-col lg:flex-row gap-6">
        <aside class="w-full lg:w-1/4 bg-white p-4 rounded shadow">
            <h3 class="font-semibold mb-3">Danh mục</h3>
            <ul class="text-sm space-y-2">
                <li><a href="#" class="text-gray-700 hover:text-red-600">Tai nghe</a></li>
                <li><a href="#" class="text-gray-700 hover:text-red-600">Chuột</a></li>
                <li><a href="#" class="text-gray-700 hover:text-red-600">Bàn phím</a></li>
            </ul>
        </aside>

        <section class="flex-1">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Sản phẩm nổi bật</h2>
                <div class="text-sm text-gray-600">Hiển thị 12 sản phẩm</div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach(range(1,12) as $i)
                    <div class="bg-white p-3 rounded shadow">
                        <div class="aspect-w-1 aspect-h-1 mb-3 bg-gray-100 flex items-center justify-center"> 
                            <span class="text-gray-400">Ảnh SP {{ $i }}</span>
                        </div>
                        <h3 class="text-sm font-medium mb-1">Sản phẩm mẫu #{{ $i }}</h3>
                        <div class="text-red-600 font-bold mb-2">1.990.000₫</div>
                        <button class="w-full py-2 bg-red-600 text-white rounded text-sm">Thêm vào giỏ</button>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
@endsection
