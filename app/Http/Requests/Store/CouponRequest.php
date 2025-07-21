<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Coupon;

class CouponRequest extends FormRequest
{
    protected $coupon;

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->coupon = $this->route('coupon');
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        } elseif ($this->isMethod('put')) {
            return $this->update();
        }

        return [];
    }

    public function createRules(): array
    {
        return [
            'code' => 'required|string|unique:coupons,code|regex:/^[A-Z0-9_]+$/',
            'type' => 'required|in:fixed,percent,free_shipping',
            'value' => 'nullable|numeric',
            'max_discount' => 'required_if:type,percent',
            'min_order_amount' => 'nullable|numeric',
            'usage_limit' => 'nullable|integer',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'required|boolean',
        ];
    }

    public function update(): array
    {
        return [
            'code' => 'required|string|regex:/^[A-Z0-9_]+$/|unique:coupons,code,' . $this->coupon->id,
            'type' => 'required|in:fixed,percent,free_shipping',
            'value' => 'nullable|numeric',
            'max_discount' => 'nullable|numeric',
            'min_order_amount' => 'nullable|numeric',
            'usage_limit' => 'nullable|integer',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after_or_equal:starts_at',
            'is_active' => 'required|boolean',
        ];
    }
}