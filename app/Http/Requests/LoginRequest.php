<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'يجب إدخال البريد الإلكتروني!',
            'email.email' => 'يجب أن يكون البريد الإلكتروني صالحاً ويحتوي على @',
            'email.exists' => 'لا يمكن العثور على هذا البريد الإلكتروني!',
            'password.required' => 'يجب إدخال كلمة المرور',
            'password.min' => 'يجب أن تتكون كلمة المرور من 6 أحرف على الأقل',
        ];
    }

    protected function failedValidation(Validator|\Illuminate\Contracts\Validation\Validator $validator) {
        $response = [
            'status' => false,
            'message' => $validator->errors(),
        ];
        throw new HttpResponseException(response()->json($response, 422));
    }

}
