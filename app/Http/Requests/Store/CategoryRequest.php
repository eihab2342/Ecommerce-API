<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    protected $cetegory;
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation()
    {
        $this->cetegory = $this->route('cetegory');
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


    public function createRules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'images' => 'nullable|mimes:jpeg,png,jpg,gif,svg,avif|max:2048',
            'carousel_images.*' => 'nullable|mimes:jpeg,png,jpg,gif,svg,avif|max:2048',
        ];
    }

    public function updateRules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $this->category->id,
            'images' => 'nullable|array',
            'carousel_images' => 'nullable|array',
        ];
    }
}