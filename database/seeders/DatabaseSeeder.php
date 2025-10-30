<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        Category::query()->insert([
            ['name' => 'Tai nghe', 'slug' => 'tai-nghe', 'featured' => true, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chuột', 'slug' => 'chuot', 'featured' => true, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bàn phím', 'slug' => 'ban-phim', 'featured' => true, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Màn hình', 'slug' => 'man-hinh', 'featured' => true, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Loa', 'slug' => 'loa', 'featured' => false, 'parent_id' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Chạy AdminSeeder và ProductSeeder
        $this->call(AdminSeeder::class);
        $this->call(ProductSeeder::class);
    }
}
