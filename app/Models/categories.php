<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class categories extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'images',
        'slug',
    ];

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_id');
    }
    public function products()
    {
        return $this->hasMany(products::class, 'category_id');
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class);
    }


    public function carouselImages()
    {
        return $this->hasMany(carouselImages::class, 'category_id')
            ->whereNull('subcategory_id')
            ->where('type', 'carousel');
    }
}