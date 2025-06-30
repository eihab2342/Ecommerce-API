<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItems extends Model
{
    protected $table = 'order_items'; // ✅ تأكيد اسم الجدول لو مختلف عن اسم الموديل

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'total_price',
        'image',
        'created_at',
    ];

    protected $casts = [
        'images' => 'array', // ✅ علشان Laravel يفك الـ JSON تلقائي
    ];

    // ✅ العلاقات (لو حبيت)
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(products::class);
    }
}