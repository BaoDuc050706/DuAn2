<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;

class ProductController extends Controller
{
    // Trang chi tiết sản phẩm
    public function show($slug)
    {
        // Lấy sản phẩm và ảnh chính
        $product = Product::with(['images' => function ($query) {
            $query->where('is_primary', 1);
        }])->where('slug', $slug)->firstOrFail();

        // Lấy đường dẫn ảnh (nếu có)
        $product->image_url = $product->images->first()->image_url ?? null;

        return view('product.show', compact('product'));
    }

    // Trang danh mục sản phẩm
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::where('category_id', $category->id)->get();

        return view('category.show', compact('category', 'products'));
    }
    public function images()
{
    return $this->hasMany(ProductImage::class);
}

}
