@extends('layouts.app')

@section('content')
@php
    // Mảng 12 ảnh GIF Azur Lane khác nhau
    $images = [
        'https://media.tenor.com/iVEAc6-SPb8AAAAi/azur-lane-chibi.gif',
        'https://media1.tenor.com/m/C2isiy1VWGYAAAAC/essex-azur-lane.gif',
        'https://media1.tenor.com/m/tmlh5sW5oBMAAAAC/essex.gif',
        'https://media1.tenor.com/m/hf-gArqQ1BkAAAAC/seseren-essex.gif',
        'https://media1.tenor.com/m/0k1t0fTY3coAAAAC/lil-cheshire-azur-lane.gif',
        'https://media1.tenor.com/m/Ammyf2mgCNAAAAAC/azur-lane-enterprise.gif', 
        'https://media.tenor.com/iVEAc6-SPb8AAAAi/azur-lane-chibi.gif',
        'https://media1.tenor.com/m/C2isiy1VWGYAAAAC/essex-azur-lane.gif',
        'https://media1.tenor.com/m/tmlh5sW5oBMAAAAC/essex.gif',
        'https://media1.tenor.com/m/hf-gArqQ1BkAAAAC/seseren-essex.gif',
        'https://media1.tenor.com/m/0k1t0fTY3coAAAAC/lil-cheshire-azur-lane.gif',
        'https://media1.tenor.com/m/Ammyf2mgCNAAAAAC/azur-lane-enterprise.gif', 
        ];
@endphp

<div class="flex flex-col lg:flex-row gap-6">
    <!-- Sidebar -->
    <aside class="w-full lg:w-1/4 bg-white p-4 rounded shadow">
        <h3 class="font-semibold mb-3">Danh mục</h3>
        <ul class="text-sm space-y-2">
            <li><a href="#" class="text-gray-700 hover:text-red-600">Tai nghe</a></li>
            <li><a href="#" class="text-gray-700 hover:text-red-600">Chuột</a></li>
            <li><a href="#" class="text-gray-700 hover:text-red-600">Bàn phím</a></li>
        </ul>
    </aside>

    <!-- Product section -->
    <section class="flex-1">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">Sản phẩm nổi bật</h2>
            <div class="text-sm text-gray-600">Hiển thị 12 sản phẩm</div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($images as $index => $img)
                <div class="bg-white p-3 rounded shadow hover:shadow-lg transition-shadow duration-200">
                    <div class="aspect-w-1 aspect-h-1 mb-3 bg-gray-100 flex items-center justify-center overflow-hidden rounded">
                        <img src="{{ $img }}" 
                             alt="Sản phẩm {{ $index + 1 }}" 
                             class="object-cover w-full h-full rounded">
                    </div>
                    <h3 class="text-sm font-medium mb-1">Sản phẩm mẫu #{{ $index + 1 }}</h3>
                    <div class="text-red-600 font-bold mb-2">1.990.000₫</div>
                    <button class="w-full py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700">Thêm vào giỏ</button>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
