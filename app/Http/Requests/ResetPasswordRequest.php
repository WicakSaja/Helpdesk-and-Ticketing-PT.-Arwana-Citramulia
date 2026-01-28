<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->hasRole('master-admin');
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Only master-admin can reset passwords'
        );
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], 422)
        );
    }

    public function rules()
    {
        return [
            'password' => 'required|min:8',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
        ];
    }
}
