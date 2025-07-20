<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'max:16', 'confirmed'],
            'phone_number' => ['sometimes', 'regex:/^01[0-2,5][0-9]{8}$/'],
        ];
    }


    public function messages()
    {
        return [
            'phone_number.regex' => 'رقم الهاتف غير صالح. تأكد أنه يبدأ بـ 01 ويتكون من 11 رقم.',
        ];
    }
}