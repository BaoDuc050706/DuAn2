<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <!-- Basic Tailwind CDN for quick styling (dev) -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        @stack('head')
    </head>
    <body class="bg-gray-100 text-gray-900">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center">
                        <a href="{{ url('/') }}" class="text-xl font-semibold text-red-600">{{ config('app.name', 'Shop') }}</a>
                    </div>
                    <nav class="hidden md:flex space-x-4">
                        <a href="#" class="text-sm text-gray-700 hover:text-red-600">Sản phẩm</a>
                        <a href="#" class="text-sm text-gray-700 hover:text-red-600">Tin tức</a>
                        <a href="#" class="text-sm text-gray-700 hover:text-red-600">Liên hệ</a>
                    </nav>
                    <div class="flex items-center gap-3">
                        <a href="#" class="text-sm text-gray-700">Đăng nhập</a>
                        <a href="#" class="px-3 py-1 bg-red-600 text-white text-sm rounded">Giỏ hàng</a>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto p-4">
            @yield('content')
        </main>

        <footer class="bg-white border-t mt-12">
            <div class="max-w-7xl mx-auto px-4 py-6 text-sm text-gray-600">
                © {{ date('Y') }} {{ config('app.name', 'Shop') }}. All rights reserved.
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>
