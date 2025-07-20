<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'slug',
        'image',
    ];


    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function products()
    {
        return $this->hasMany(Products::class, 'subcategory_id');
    }
    public function carouselImages()
    {
        return $this->hasMany(carouselImages::class, 'subcategory_id')
            ->where('type', 'carousel');
    }
}