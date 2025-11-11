<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['name' => 'Tai nghe', 'slug' => 'tai-nghe', 'featured' => true],
            ['name' => 'Chuột', 'slug' => 'chuot', 'featured' => true],
            ['name' => 'Bàn phím', 'slug' => 'ban-phim', 'featured' => true],
            ['name' => 'Màn hình', 'slug' => 'man-hinh', 'featured' => true],
            ['name' => 'Loa', 'slug' => 'loa', 'featured' => false],
            ['name' => 'Laptop', 'slug' => 'laptop', 'featured' => true],
        ];

        foreach ($items as $it) {
            Category::updateOrCreate(
                ['slug' => $it['slug']],
                [
                    'name' => $it['name'],
                    'featured' => (bool) ($it['featured'] ?? false),
                    'parent_id' => null,
                ]
            );
        }
    }
}
