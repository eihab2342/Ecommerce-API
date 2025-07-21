<?php

namespace App\Http\Requests\Store;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }


    public function rules(): array
    {
        return [
            'image_path' => 'required',
            'type' => 'required|string',
            'belongsTo' => 'nullable|string|max:255',
        ];
    }
}