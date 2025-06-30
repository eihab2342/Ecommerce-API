<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone_number',
        'total_price',
        'original_price',
        'status',
        'payment_method',
        'shipping_address',
        'village',
        'city',
        'governorate',
        'billing_address',
        'comments',
        'ordered_at',
        'created_at'
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    // علاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة مع العناصر (المنتجات) داخل الطلب
    public function items()
    {
        return $this->hasMany(OrderItems::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}