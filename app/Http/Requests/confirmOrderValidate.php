<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; // ✅ Import Validator
use Illuminate\Http\Exceptions\HttpResponseException; // ✅ Import HttpResponseException

class ConfirmOrderValidate extends FormRequest
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
            'fullname' => 'required',
            'user_email'  => 'required|email',
            'user_phone'    => 'required',
        ];
    }

    public function messages()
    {
        return [
            'id.required'       => 'ID không được để trống!',
            'fullname.required' => 'Họ và tên khách hàng không được để trống!',
            'user_email.required'  => 'Email không được để trống!',
            'user_email.email'  => 'Vui lòng nhập đúng định dạng email!',
            'user_phone.required'    => 'Số điện thoại không được để trống!',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'errors'  => $validator->errors(),
            'message' => 'Validation Failed'
        ], 422));
    }
}