<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class carouselImages extends Model
{
    //
    protected $fillable = ['image_path', 'belongs_to', 'type', 'category_id', 'subcategory_id'];

    // في موديل Photo
    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    // public function subcategory()
    // {
    //     return $this->belongsTo(Subcategory::class);
    // }
}