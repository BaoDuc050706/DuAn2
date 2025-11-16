<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        
        if (Schema::hasTable('products')) {
            $products = DB::table('products as p')
                // Join lấy ảnh chính, nếu không có thì lấy ảnh mặc định của sản phẩm
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
                ->where(function ($q) use ($query) {
                    $q->where('p.name', 'LIKE', "%{$query}%")
                      ->orWhere('p.description', 'LIKE', "%{$query}%");
                })
                ->get();

            // Đảm bảo mỗi sản phẩm đều có trường 'image' hợp lệ
            $products = $products->map(function ($product) {
                // Nếu không có ảnh, gán ảnh mặc định
                if (empty($product->image)) {
                    $product->image = '/images/no-image.png';
                }
                return $product;
            });
        } else {
            $products = collect();
        }

        return view('search', compact('products', 'query'));
    }
}
