<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        $products = collect();
        $activeCategory = null;
        if (Schema::hasTable('products')) {
            $products = DB::table('products as p')
                ->leftJoin('product_images as pi', function ($join) {
                    $join->on('pi.product_id', '=', 'p.id')
                        ->where('pi.is_primary', '=', 1);
                })
                ->select([
                    'p.id',
                    'p.slug',
                    'p.name',
                    'p.price',
                    'p.discount',
                    'p.stock',
                    'p.connection',
                    'p.rgb',
                    DB::raw('COALESCE(pi.image_url, p.image) as image'),
                ])
                ->orderByDesc('p.created_at');

            // If category filter provided, try to find Category by slug or name (case-insensitive)
            $categoryQuery = trim((string) $request->query('category', ''));
            if ($categoryQuery !== '') {
                $cat = null;
                try {
                    // first try exact slug
                    $cat = Category::where('slug', $categoryQuery)->first();
                    // if not found, try name (case-insensitive)
                    if (! $cat) {
                        $cat = Category::whereRaw('LOWER(name) = ?', [strtolower($categoryQuery)])->first();
                    }
                    // also try name LIKE as fallback
                    if (! $cat) {
                        $cat = Category::where('name', 'like', "%{$categoryQuery}%")->first();
                    }
                } catch (\Throwable $e) {
                    $cat = null;
                }

                if ($cat) {
                    $activeCategory = $cat;
                    // Filter by category_id column if it exists
                    if (Schema::hasColumn('products', 'category_id')) {
                        $products = $products->where('p.category_id', $cat->id);
                    }
                }
            }

            // Kiểm tra xem có yêu cầu hiển thị tất cả sản phẩm không
            $showAll = $request->query('show_all', false);
            if ($showAll) {
                $products = $products->get(); // Hiển thị tất cả sản phẩm
            } else {
                $products = $products->limit(isset($activeCategory) ? 20 : 8)->get(); // Giới hạn như cũ
            }
        }

        // Danh mục cho phần "Danh mục nổi bật" (không bao gồm Laptop)
        $categories = collect();
        $categoryTable = $this->firstExistingTable(['categories', 'category', 'danh_muc', 'danhmuc']);
        if ($categoryTable !== null) {
            $categories = DB::table($categoryTable)
                ->select(DB::raw('id, name, slug'))
                ->where('name', '!=', 'Laptop')
                ->orderBy('name')
                ->limit(8)
                ->get();
        }

        // Danh mục cho dropdown menu (bao gồm cả Laptop)
        $featuredCategories = collect();
        if ($categoryTable !== null) {
            $featuredCategories = DB::table($categoryTable)
                ->select(DB::raw('id, name, slug'))
                ->orderBy('name')
                ->limit(8)
                ->get();
        }

        return view('home', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'featuredCategories' => $featuredCategories, // Danh mục cho dropdown menu (bao gồm Laptop)
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
