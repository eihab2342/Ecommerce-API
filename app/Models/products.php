<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class products extends Model
{
    // لو اسم الجدول مش بصيغة الجمع التلقائي أو مختلف عن 'products'
    // protected $table = 'products';

    // السماح بملء الحقول التالية
    protected $fillable = [
        'name',
        'description',
        'price',
        'old_price',
        'cost_price',
        'quantity',
        'category_id',
        'subcategory_id',
        'created_at',
        'updated_at'
    ];

    // العلاقات:

    public function category(): BelongsTo
    {
        return $this->belongsTo(categories::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class, 'subcategory_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(images::class, 'product_id');
    }
}
