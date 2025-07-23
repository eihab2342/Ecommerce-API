<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Products;

class ProductRequest extends FormRequest
{
    protected ?Products $product = null;

    public function authorize(): bool
    {
        $this->prepareForValidation();
        /** @var \App\Models\User $user */
        $user = auth('sanctum')->user();

        return $user && $user->can($this->actionPermission(), $this->product ?? Products::class);
    }

    protected function actionPermission(): string
    {
        return $this->isMethod('post') ? 'create' : 'update';
    }

    protected function prepareForValidation(): void
    {
        $this->product = $this->route('product');
    }

    public function rules(): array
    {
        return $this->isMethod('post') ? $this->createRules() : $this->updateRules();
    }

    protected function createRules(): array
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

    protected function updateRules(): array
    {
        return $this->createRules();
    }
}