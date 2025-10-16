<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $catalog = [
            'tai-nghe-gaming-x1' => [
                'name' => 'Tai nghe Gaming X1',
                'price' => 1299000,
                'image' => 'https://images.unsplash.com/photo-1518445282155-7950073c585e?q=80&w=1200&auto=format&fit=crop',
                'description' => 'Tai nghe gaming âm thanh vòm, mic lọc ồn, đệm tai êm ái.',
            ],
            'chuot-khong-day-pro' => [
                'name' => 'Chuột không dây Pro',
                'price' => 799000,
                'image' => 'https://images.unsplash.com/photo-1587820650444-3c6691f92b9f?q=80&w=1200&auto=format&fit=crop',
                'description' => 'Chuột không dây độ trễ thấp, cảm biến chính xác, pin bền.',
            ],
            'ban-phim-co-rgb' => [
                'name' => 'Bàn phím cơ RGB',
                'price' => 1599000,
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1200&auto=format&fit=crop',
                'description' => 'Bàn phím cơ switch bền bỉ, đèn RGB nhiều hiệu ứng.',
            ],
            'man-hinh-27-144hz' => [
                'name' => 'Màn hình 27" 144Hz',
                'price' => 4999000,
                'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1200&auto=format&fit=crop',
                'description' => 'Màn hình gaming 27 inch, 144Hz, thời gian phản hồi nhanh.',
                'man-hinh-27-144hz' => [
                    'name' => 'Màn hình 27" 144Hz',
                    'price' => 4999000,
                    'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?q=80&w=1200&auto=format&fit=crop',
                    'description' => 'Màn hình gaming 27 inch, 144Hz, thời gian phản hồi nhanh.',
                ],
            ]
        ];

        $product = $catalog[$slug] ?? null;

        abort_if($product === null, 404);

        return view('product.show', [
            'slug' => $slug,
            'product' => $product,
        ]);
    }
}


