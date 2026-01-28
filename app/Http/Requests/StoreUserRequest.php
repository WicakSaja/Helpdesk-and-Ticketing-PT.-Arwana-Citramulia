<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->hasRole('master-admin');
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Only master-admin can create users'
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
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|min:8',
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|string|in:helpdesk,technician,supervisor',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama user wajib diisi',
            'name.max' => 'Nama user maksimal 100 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus format yang valid',
            'email.unique' => 'Email sudah terdaftar',
            'phone.required' => 'Nomor telepon wajib diisi',
            'phone.unique' => 'Nomor telepon sudah terdaftar',
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'roles.required' => 'Role wajib dipilih minimal satu',
            'roles.*.in' => 'Role harus salah satu dari: helpdesk, technician, supervisor',
            'department_id.exists' => 'Department yang dipilih tidak valid',
        ];
    }
}
