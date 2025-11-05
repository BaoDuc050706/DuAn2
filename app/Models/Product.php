<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'stock',
        'description',
        'image',
        'category_id', // nếu dùng relation
        'discount',
        'connection',
        'rgb',
    ];

    // Relation đến category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
