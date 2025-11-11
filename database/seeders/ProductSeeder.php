<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $cat = fn(string $slug) => (int) DB::table('categories')->where('slug', $slug)->value('id');

        $rows = [
            [
                'slug' => 'ban-phim-60-rgb',
                'name' => 'Bàn phím 60% RGB',
                'category_slug' => 'ban-phim',
                'price' => 1399000.00,
                'discount' => 0.00,
                'stock' => 65,
                'connection' => 'Có dây',
                'rgb' => 1,
                'description' => 'Layout 60% gọn.',
                'image' => 'ban_phim_razer_blackwidow_v4_75__hotswap_31a01210d8bd43c585497608f49790fb.jpg',
            ],
            [
                'slug' => 'ban-phim-co-rgb',
                'name' => 'Bàn phím cơ RGB',
                'category_slug' => 'ban-phim',
                'price' => 1599000.00,
                'discount' => 0.00,
                'stock' => 70,
                'connection' => 'Có dây',
                'rgb' => 1,
                'description' => 'Switch bền, LED RGB.',
                'image' => 'aula-f75-seiya-switch-xanh-duong-trang.png',
            ],
            [
                'slug' => 'ban-phim-khong-day-low-profile',
                'name' => 'Bàn phím không dây Low-Profile',
                'category_slug' => 'ban-phim',
                'price' => 1756575.00,
                'discount' => 7.50,
                'stock' => 45,
                'connection' => 'Không dây',
                'rgb' => 1,
                'description' => 'Low-profile mỏng nhẹ.',
                'image' => 'mongesk-fun60-mb-vi1739859692.jpg',
            ],
            [
                'slug' => 'chuot-gaming-ultra',
                'name' => 'Chuột Gaming Ultra',
                'category_slug' => 'chuot',
                'price' => 1103080.00,
                'discount' => 8.00,
                'stock' => 80,
                'connection' => 'Có dây',
                'rgb' => 1,
                'description' => 'Cảm biến chính xác.',
                'image' => '250_11603_chuot_game_khong_day_logitech_g502x_rgb_plus_lightspeed_trang_1.jpg',
            ],
            [
                'slug' => 'chuot-van-phong-silent',
                'name' => 'Chuột văn phòng Silent',
                'category_slug' => 'chuot',
                'price' => 399000.00,
                'discount' => 0.00,
                'stock' => 150,
                'connection' => 'Không dây',
                'rgb' => 0,
                'description' => 'Nhấp êm, bền pin.',
                'image' => 'images.jpg',
            ],
            [
                'slug' => 'chuot-khong-day-pro',
                'name' => 'Chuột không dây Pro',
                'category_slug' => 'chuot',
                'price' => 799000.00,
                'discount' => 0.00,
                'stock' => 120,
                'connection' => 'Không dây',
                'rgb' => 0,
                'description' => 'Độ trễ thấp.',
                'image' => '2022_4_4_637846769675161901_gaming-gear.jpg',
            ],
            [
                'slug' => 'tai-nghe-studio-hi-fi',
                'name' => 'Tai nghe Studio Hi-Fi',
                'category_slug' => 'tai-nghe',
                'price' => 2069100.00,
                'discount' => 10.00,
                'stock' => 25,
                'connection' => 'Có dây',
                'rgb' => 0,
                'description' => 'Âm thanh trung thực.',
                'image' => 'kraken-v3-hypersense-1_45b84b4db29841dcb8dcefa1d043c80d_medium.jpg',
            ],
            [
                'slug' => 'tai-nghe-wireless-pro',
                'name' => 'Tai nghe Wireless Pro',
                'category_slug' => 'tai-nghe',
                'price' => 1614050.00,
                'discount' => 5.00,
                'stock' => 40,
                'connection' => 'Không dây',
                'rgb' => 1,
                'description' => 'Tai nghe không dây pin bền.',
                'image' => 'tai-nghe-wireless-pro.jpg',
            ],
        ];

        foreach ($rows as $r) {
            Product::updateOrCreate(
                ['slug' => $r['slug']],
                [
                    'name' => $r['name'],
                    'category_id' => $cat($r['category_slug']),
                    'price' => $r['price'],
                    'discount' => $r['discount'],
                    'stock' => $r['stock'],
                    'connection' => $r['connection'],
                    'rgb' => (bool) $r['rgb'],
                    'description' => $r['description'],
                    'image' => $r['image'],
                ]
            );
        }
    }
}
