<?php

namespace App\Services\Admin\Coupon;

use App\Models\coupon;
use App\Repositories\Admin\Coupon\CouponRepository;
use Illuminate\Support\Facades\Request;

class CouponService
{
    public function __construct(protected CouponRepository $couponRepo) {}

    public function index()
    {
        return $this->couponRepo->index();
    }
    public function store(array $data)
    {
        if (Coupon::where('code', $data['code'])->exists()) {
            return response()->json([
                'message' => 'كود الكوبون موجود بالفعل',
            ], 400);
        }

        if ($data['type'] === 'free_shipping') {
            $data['value'] = null;
            $data['max_discount'] = null;
        }

        if (in_array($data['type'], ['percent', 'fixed'])) {
            if ($data['value'] === null) {
                return response()->json([
                    'message' => 'قيمة الخصم مطلوبة',
                ], 400);
            }

            if ($data['type'] === 'percent' && $data['max_discount'] === null) {
                return response()->json([
                    'message' => 'الحد الأقصى للخصم مطلوب',
                ], 400);
            }
        }

        return $this->couponRepo->store($data);
    }
    public function update(array $data, Coupon $coupon)
    {
        if ($data['type'] === 'free_shipping') {
            $data['value'] = null;
            $data['max_discount'] = null;
        }

        if (in_array($data['type'], ['percent', 'fixed'])) {
            if (!isset($data['value'])) {
                throw new \Exception('قيمة الخصم مطلوبة');
            }

            if ($data['type'] === 'percent' && !isset($data['max_discount'])) {
                throw new \Exception('الحد الأقصى للخصم مطلوب');
            }
        }

        return $this->couponRepo->update($data, $coupon);
    }
    public function destroy($id)
    {
        return $this->couponRepo->destroy($id);
    }
    public function toggle($id, Request $request)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $coupon = $this->couponRepo->findOrFail($id);
        $coupon->is_active = $validated['is_active'];
        $coupon->save();

        return $coupon;
    }
}
