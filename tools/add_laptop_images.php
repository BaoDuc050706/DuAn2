<?php
// Script: add_laptop_images.php
// Usage: php tools/add_laptop_images.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

$publicImageDir = __DIR__ . '/../public/image';
if (! is_dir($publicImageDir)) {
    mkdir($publicImageDir, 0755, true);
}

// find laptop category id if exists
$laptopCategory = null;
try {
    $laptopCategory = DB::table('categories')->where('slug', 'laptop')->first();
} catch (Throwable $e) {
    $laptopCategory = null;
}

if ($laptopCategory) {
    $products = Product::where(function($q) use ($laptopCategory) {
        $q->where('category_id', $laptopCategory->id)
          ->orWhereRaw('LOWER(name) LIKE ?', ['%laptop%']);
    })->get();
} else {
    $products = Product::whereRaw('LOWER(name) LIKE ?', ['%laptop%'])->get();
}

if ($products->isEmpty()) {
    echo "No products found with name containing 'laptop'.\n";
    exit(0);
}

echo "Found {$products->count()} product(s) with 'laptop' in the name.\n";

$hasProductImages = Schema::hasTable('product_images');
$hasImageColumn = Schema::hasColumn('products', 'image');

foreach ($products as $p) {
    $slugPart = $p->slug ?? null;
    if (! $slugPart) {
        $slugPart = preg_replace('/[^a-z0-9\-]+/i', '-', strtolower($p->name));
        $slugPart = trim($slugPart, '-');
        if ($slugPart === '') $slugPart = 'product-' . $p->id;
    }
    $filename = $slugPart . '.svg';
    $filepath = $publicImageDir . '/' . $filename;
    $webpath = '/image/' . $filename; // URL used in views

    // create a simple SVG placeholder
    $svg = <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="400" height="300">
  <rect width="100%" height="100%" fill="#f3f4f6"/>
  <rect x="8" y="8" width="384" height="284" fill="#e5e7eb" stroke="#d1d5db" stroke-width="2" rx="8"/>
  <text x="50%" y="50%" font-family="Arial, Helvetica, sans-serif" font-size="20" fill="#111827" dominant-baseline="middle" text-anchor="middle">{NAME}</text>
  <text x="50%" y="70%" font-family="Arial, Helvetica, sans-serif" font-size="14" fill="#6b7280" dominant-baseline="middle" text-anchor="middle">Product ID: {ID}</text>
</svg>
SVG;
    $svg = str_replace(['{NAME}','{ID}'], [htmlspecialchars($p->name), $p->id], $svg);

    file_put_contents($filepath, $svg);
    echo "Created image: {$filepath}\n";

    if ($hasProductImages) {
        try {
            // remove existing primary images for this product (optional)
            DB::table('product_images')->where('product_id', $p->id)->where('is_primary', 1)->delete();
            DB::table('product_images')->insert([
                'product_id' => $p->id,
                'image_url' => $webpath,
                'is_primary' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "Inserted product_images entry for product_id={$p->id}\n";
        } catch (Throwable $e) {
            echo "Failed to insert into product_images for product_id={$p->id}: " . $e->getMessage() . "\n";
        }
    } elseif ($hasImageColumn) {
        try {
            DB::table('products')->where('id', $p->id)->update(['image' => $webpath]);
            echo "Updated products.image for id={$p->id}\n";
        } catch (Throwable $e) {
            echo "Failed to update products.image for id={$p->id}: " . $e->getMessage() . "\n";
        }
    } else {
        echo "No product_images table and no products.image column — image file created but DB not updated for product id={$p->id}\n";
    }
}

echo "Done.\n";
