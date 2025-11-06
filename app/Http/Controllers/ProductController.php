<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function show($slug)
    {
        // Lấy sản phẩm với hình ảnh từ bảng product_images
        $product = DB::table('products as p')
            ->leftJoin('product_images as pi', function ($join) {
                $join->on('pi.product_id', '=', 'p.id')
                     ->where('pi.is_primary', '=', 1);
            })
            ->where('p.slug', $slug)
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
                'p.category_id',
                DB::raw('pi.image_url as image'),
            ])
            ->first();

        if (!$product) {
            abort(404);
        }

        return view('product.show', compact('product'));
    }

    // Thêm phương thức này
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->get();

        return view('category.show', compact('category', 'products'));
    }
}
