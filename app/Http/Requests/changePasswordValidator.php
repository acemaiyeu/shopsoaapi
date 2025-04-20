<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator; // ✅ Import Validator
use Illuminate\Http\Exceptions\HttpResponseException; // ✅ Import HttpResponseException

class changePasswordValidator extends FormRequest
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
            'password'       => 'required',
            'new_password' => 'required',
           
        ];
    }

    public function messages()
    {
        return [
            'password.required'       => 'Mật khẩu không được để trống!',
            'new_password.required' => 'Mật khẩu mới không được để trống!',
            
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