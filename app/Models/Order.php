<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
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
    ];

    protected $casts = [
        'extra_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItems::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}