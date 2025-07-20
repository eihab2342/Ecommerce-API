<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class SignupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')],
            'password' => ['required', Password::min(8)->max(16)],
            'phone_number' => ['required', 'regex:/^01[0-2,5][0-9]{8}$/'],
        ];
    }

    public function messages()
    {
        return [
            'phone_number.regex' => 'رقم الهاتف غير صالح، يجب أن يبدأ بـ 01 ويتكون من 11 رقم.',
        ];
    }
}