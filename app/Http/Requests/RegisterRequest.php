<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;

class RegisterRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'mobile_number' => 'required|string|unique:users,mobile_number',
            'username' => 'required|string',
            'password' => 'required|string|min:6',
            'address' => 'required|string'
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'يجب إدخال اسم المستخدم',
            'email.required' => 'يجب إدخال البريد الإلكتروني',
            'email.email' => 'يجب أن يكون البريد الإلكتروني صالحاً ويحتوي على @',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقا',
            'mobile_number.required' => 'يجب إدخال رقم الهاتف',
            'mobile_number.digits' => 'رقم الهاتف يجب أن يتكون من 10 أرقام',
            'mobile_number.unique' => 'رقم الهاتف مسجل مسبقا',
            'address.required' => 'يجب إدخال عنوان المستخدم',
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
