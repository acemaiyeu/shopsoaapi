<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class confirmOrderValidate extends FormRequest
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
            'id'       => 'required',
            'username' => 'required',
            'address'  => 'required',
            'phone' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'ID không được để trống!',
            'username.required' => 'Họ và tên khách hàng không được để trống!',
            'address.required' => 'Địa chỉ nhận hàng không được để trống!',
            'phone.required' => 'Số điện thoại không được để trống!',
            // 'email.unique' => 'Email đã tồn tại!',
            // 'password.required' => 'Mật khẩu không được để trống!',
            // 'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự!',
            // 'password.confirmed' => 'Mật khẩu nhập lại không khớp!',
        ];
    }
}
