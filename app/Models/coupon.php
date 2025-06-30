<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class coupon extends Model
{
    //
    protected $fillable = [
        'code',
        'type',
        'value',
        'max_discount',
        'min_order_amount',
        'user_id',
        'is_active',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at'
    ];

    protected $dates = ['starts_at', 'expires_at'];

    public function isValid()
    {
        $now = Carbon::now();

        return $this->is_active &&
            ($this->starts_at == null || $this->starts_at <= $now) &&
            ($this->expires_at == null || $this->expires_at >= $now) &&
            ($this->usage_limit === null || $this->used_count < $this->usage_limit);
    }

    // Coupon.php
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')->withTimestamps();
    }
    public function categories()
    {
        return $this->belongsToMany(categories::class);
    }
}