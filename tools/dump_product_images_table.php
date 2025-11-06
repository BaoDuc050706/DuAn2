<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
try {
    if (!Schema::hasTable('product_images')) {
        echo "No product_images table\n";
        exit;
    }
    $rows = DB::table('product_images')->select('id', 'product_id', 'image_url', 'is_primary')->limit(50)->get();
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage();
}
