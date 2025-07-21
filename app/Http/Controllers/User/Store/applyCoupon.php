<?php

namespace App\Http\Controllers\User\Store;

use App\Http\Controllers\Controller;

use App\Models\coupon;
use App\Models\coupon_user;
use Cache;
use Illuminate\Http\Request;

class ApplyCoupon
{
    public function applyCoupon(Request $request)
    {
        // Log::info($request->all());
        $code = $request->input('coupon_code');
        $cartTotal = $request->input('cart_total');
        $categoryIdsInCart = $request->input('category_ids'); // array of IDs
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['error' => '❌ يجب تسجيل الدخول لتطبيق الكوبون.'], 401);
        }

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['error' => '❌ لا يوجد كوبون'], 404);
        }

        if (!$coupon->is_active) {
            return response()->json(['error' => '❌ الكوبون غير مفعل حالياً.'], 400);
        }

        if (!$coupon->isValid()) {
            return response()->json(['error' => '❌ الكوبون غير صالح أو منتهي'], 400);
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return response()->json(['error' => '❌ تم استخدام الكوبون الحد الأقصى المسموح.'], 400);
        }


        $cacheKey = 'coupon_applied_user_' . $user->id . '_coupon_' . $coupon->id;

        if (Cache::has($cacheKey)) {
            $cached = Cache::get($cacheKey);

            if (now()->greaterThan($cached['valid_until'])) {
                Cache::forget($cacheKey);
                return response()->json(['error' => '❌ الكوبون منتهي الصلاحية.'], 400);
            }

            return response()->json([
                'success' => true,
                'discount' => $cached['discount'],
                'new_total' => round($cartTotal - $cached['discount'], 2),
                'message' => '✅ الكوبون مستخدم مسبقًا: ' . $cached['discount_text'],
            ]);
        }


        if ($coupon->user_id !== null && $coupon->user_id !== $user->id) {
            return response()->json(['error' => '❌ هذا الكوبون غير مخصص لك.'], 403);
        }

        if ($coupon->min_order_amount && $cartTotal < $coupon->min_order_amount) {
            return response()->json([
                'error' => '❌ الحد الأدنى لتطبيق الكوبون هو ' . $coupon->min_order_amount . ' جنيه.',
            ], 400);
        }

        $discount = 0;
        $discountText = '';

        switch ($coupon->type) {
            case 'fixed':
                $discount = $coupon->value;
                $discountText = 'خصم ثابت بقيمة ' . $coupon->value . ' جنيه';
                break;

            case 'percent':
                $discount = ($cartTotal * $coupon->value) / 100;
                $discountText = 'خصم ' . $coupon->value . '% من قيمة السلة';
                break;

            case 'free_shipping':
                $discount = 0;
                $discountText = 'شحن مجاني';
                break;
        }

        if ($coupon->max_discount !== null && $discount > $coupon->max_discount) {
            $discount = $coupon->max_discount;
            $discountText .= ' (تم تحديد الحد الأقصى للخصم)';
        }

        $newTotal = max($cartTotal - $discount, 0);

        $expireAt = $coupon->end_date ?? now()->addMinutes(60); // نقدر نعدل المدة حسب نوع الكوبون

        Cache::put($cacheKey, [
            'code' => $coupon->code,
            'discount' => round($discount, 2),
            'discount_text' => $discountText,
            'valid_until' => $expireAt
        ], $expireAt);

        coupon_user::updateOrInsert(
            ['user_id' => $user->id, 'coupon_id' => $coupon->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        return response()->json([
            'success' => true,
            'discount' => round($discount, 2),
            'new_total' => round($newTotal, 2),
            'message' => '✅ تم تطبيق الكوبون بنجاح: ' . $discountText,
        ]);
    }
}