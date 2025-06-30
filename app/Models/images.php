<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    protected $fillable = ['product_id', 'images_path', 'is_main'];

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}