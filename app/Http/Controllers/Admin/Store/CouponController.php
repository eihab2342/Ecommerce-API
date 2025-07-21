<?php

namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\CouponRequest;
use App\Http\Resources\Admin\Coupon\CouponResource;
use App\Models\coupon;
use App\Services\Admin\Coupon\CouponService;
use Illuminate\Support\Facades\Request;

class CouponController extends Controller
{

    public function __construct(private CouponService $couponService) {}
    public function index()
    {
        $coupons = $this->couponService->index();
        // return CouponResource::collection($coupons);
    }
    public function store(CouponRequest $request)
    {
        $validated = $request->validated();
        $coupon = $this->couponService->store($validated);

        return response()->json([
            'message' => 'تم إنشاء الكوبون بنجاح',
            'coupon' => $coupon
        ], 201);
    }
    public function update(CouponRequest $request, Coupon $coupon)
    {
        try {
            $updatedCoupon = $this->couponService->update($request->validated(), $coupon);

            return response()->json([
                'message' => 'تم تحديث الكوبون بنجاح',
                'coupon' => $updatedCoupon,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function destroy($id)
    {
        $this->couponService->destroy($id);

        return response()->json(['message' => 'تم حذف الكوبون بنجاح']);
    }
    public function toggle($id, Request $request)
    {
        $coupon = $this->couponService->toggle($id, $request);

        return response()->json([
            'message' => 'تم تغيير حالة التفعيل بنجاح',
            'coupon' => $coupon,
        ]);
    }
}