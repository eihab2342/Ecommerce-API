<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class coupon_user extends Model
{
    //
    protected $table = 'coupon_user';
    protected $fillable = ['user_id', 'coupon_id', 'created_at'];
}
