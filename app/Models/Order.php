<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'full_name',
        'email',
        'phone',
        'address',
        'payment_method',
        'subtotal',
        'shipping',
        'total',
        'status',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];

    // Relation với User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope để lọc đơn hàng theo trạng thái
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
