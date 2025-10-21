<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index(): View
    {
        $products = collect();
        if (Schema::hasTable('products')) {
            $products = DB::table('products as p')
                ->leftJoin('product_images as pi', function ($join) {
                    $join->on('pi.product_id', '=', 'p.id')
                         ->where('pi.is_primary', '=', 1);
                })
                ->select([
                    'p.slug',
                    'p.name',
                    'p.price',
                    'p.discount',
                    'p.stock',
                    'p.connection',
                    'p.rgb',
                    DB::raw('pi.image_url as image'),
                ])
                ->orderByDesc('p.created_at')
                ->limit(8)
                ->get();
        }

        $categories = collect();
        $categoryTable = $this->firstExistingTable(['categories', 'category', 'danh_muc', 'danhmuc']);
        if ($categoryTable !== null) {
            $categories = DB::table($categoryTable)
                ->select(DB::raw('id, name, slug'))
                ->orderBy('name')
                ->limit(8)
                ->get();
        }

        return view('home', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    private function firstExistingTable(array $candidates): ?string
    {
        foreach ($candidates as $table) {
            if (Schema::hasTable($table)) {
                return $table;
            }
        }
        return null;
    }
}