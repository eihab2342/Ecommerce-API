<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'clientName' => 'required|string',
            'clientEmail' => 'required|email',
            'clientPhone' => 'required|string',
            'shipping_address' => 'required|string',
            'billing_address' => 'required|string',
            'village' => 'required|string',
            'city' => 'required|string',
            'governorate' => 'required|string',
            'payment_method' => 'required|string',
            'comments' => 'nullable|string',
            'total' => 'required|decimal:0,2',
            'items' => 'required|array',
            'items.*.product_id' => 'required|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }
}
