<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterValidator extends FormRequest
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
            // 'email'  => 'required|email|unique:users,email',
            // 'password' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'Tài khoản không được để trống!',
            'email.email' => 'Tài khoản phải là dạng email!',
            'email.unique' => 'Tài khoản đã tồn tại trên hệ thống!',
            'password.required' => 'Mật khẩu không được để trống!',
            // 'password.min:6' => 'Mật khẩu có độ dài từ 6 kí tự!',
        ];
    }
}
