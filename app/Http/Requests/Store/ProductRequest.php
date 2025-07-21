<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest

{

    public function authorize(): bool
    {
        return false;
    }


    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'old_price' => 'nullable|numeric',
            'cost_price' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'images.*' => 'required|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
        ];
    }
}