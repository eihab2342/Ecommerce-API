<?php

namespace App\Repositories\Admin\Coupon;

use App\Models\coupon;
use Illuminate\Support\Facades\Request;

class CouponRepository
{
    public function index()
    {
        return Coupon::all();
    }
    public function store(array $data)
    {
        return coupon::create($data);
    }
    public function update(array $data, Coupon $coupon)
    {
        $coupon->update($data);
        return $coupon;
    }
    public function destroy($id)
    {
        $coupon = Coupon::findOrFail($id);
        return $coupon->delete();
    }
    public function findOrFail($id)
    {
        return Coupon::findOrFail($id);
    }
}