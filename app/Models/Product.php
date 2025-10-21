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
        'description',
        'image',
        'category_id', // nếu dùng relation
        'discount',
    ];

    // Relation đến category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
