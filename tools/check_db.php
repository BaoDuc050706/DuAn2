<?php
// Quick DB inspection helper — boots the app and prints some checks
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

echo "--- Table existence ---\n";
echo "categories: "; var_export(Schema::hasTable('categories')); echo "\n";
echo "danh_muc:  "; var_export(Schema::hasTable('danh_muc')); echo "\n";
echo "category:  "; var_export(Schema::hasTable('category')); echo "\n";

echo "--- Category with slug 'laptop' ---\n";
try {
    $cat = Category::where('slug', 'laptop')->first();
    var_export($cat ? $cat->toArray() : null);
} catch (Throwable $e) {
    echo "Exception when querying Category: " . $e->getMessage() . "\n";
}

echo "\n--- products table checks ---\n";
echo "has column category_id: "; var_export(Schema::hasColumn('products', 'category_id')); echo "\n";

try {
    $rows = DB::table('products')->select('id','name','category_id')->limit(8)->get()->toArray();
    echo "sample products:\n"; var_export($rows); echo "\n";
} catch (Throwable $e) {
    echo "Exception when querying products: " . $e->getMessage() . "\n";
}

echo "\nDone.\n";

// Also print all categories for convenience
echo "\n--- All categories (id,name,slug) ---\n";
try {
    $all = App\Models\Category::select('id','name','slug')->orderBy('id')->get()->toArray();
    foreach ($all as $c) {
        echo "- id={$c['id']} name={$c['name']} slug={$c['slug']}\n";
    }
} catch (Throwable $e) {
    echo "Cannot list categories: " . $e->getMessage() . "\n";
}
