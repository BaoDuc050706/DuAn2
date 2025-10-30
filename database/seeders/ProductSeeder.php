<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::create([
            'name' => 'Chuột Gaming Logitech G502',
            'slug' => 'chuot-gaming-logitech-g502',
            'price' => 1200000,
            'stock' => 50,
            'discount' => 10,
            'description' => 'Chuột gaming cao cấp với độ nhạy cao và thiết kế ergonomic',
            'category_id' => 2
        ]);

        Product::create([
            'name' => 'Bàn phím cơ Razer BlackWidow',
            'slug' => 'ban-phim-co-razer-blackwidow',
            'price' => 2500000,
            'stock' => 30,
            'discount' => 15,
            'description' => 'Bàn phím cơ gaming với switch xanh và đèn RGB',
            'category_id' => 3
        ]);

        Product::create([
            'name' => 'Tai nghe Gaming HyperX Cloud II',
            'slug' => 'tai-nghe-gaming-hyperx-cloud-ii',
            'price' => 1800000,
            'stock' => 25,
            'discount' => 5,
            'description' => 'Tai nghe gaming với âm thanh 7.1 surround và mic khử nhiễu',
            'category_id' => 1
        ]);

        Product::create([
            'name' => 'Màn hình Gaming ASUS ROG',
            'slug' => 'man-hinh-gaming-asus-rog',
            'price' => 8000000,
            'stock' => 15,
            'discount' => 20,
            'description' => 'Màn hình gaming 27 inch với tần số quét 144Hz',
            'category_id' => 4
        ]);

        Product::create([
            'name' => 'Loa Gaming Razer Nommo',
            'slug' => 'loa-gaming-razer-nommo',
            'price' => 3200000,
            'stock' => 20,
            'discount' => 8,
            'description' => 'Loa gaming 2.0 với âm thanh rõ ràng và bass mạnh',
            'category_id' => 5
        ]);
    }
}
