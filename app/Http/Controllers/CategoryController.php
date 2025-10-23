<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        // Lấy sản phẩm với hình ảnh từ bảng product_images
        $products = DB::table('products as p')
            ->leftJoin('product_images as pi', function ($join) {
                $join->on('pi.product_id', '=', 'p.id')
                     ->where('pi.is_primary', '=', 1);
            })
            ->where('p.category_id', $category->id)
            ->select([
                'p.id',
                'p.name',
                'p.slug',
                'p.price',
                'p.discount',
                'p.stock',
                'p.connection',
                'p.rgb',
                'p.description',
                DB::raw('pi.image_url as image'),
            ])
            ->orderByDesc('p.created_at')
            ->get();

        return view('category.show', compact('category', 'products'));
    }
}
