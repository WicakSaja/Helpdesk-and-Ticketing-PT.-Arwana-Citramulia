<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->hasRole('master-admin');
    }

    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException(
            'Only master-admin can update users'
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
        $userId = $this->route('user')->id ?? $this->route('user');
        
        return [
            'name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|email|unique:users,email,' . $userId,
            'phone' => 'sometimes|required|string|max:20|unique:users,phone,' . $userId,
            'department_id' => 'nullable|exists:departments,id',
            'roles' => 'sometimes|required|array|min:1',
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
            'roles.required' => 'Role wajib dipilih minimal satu',
            'roles.*.in' => 'Role harus salah satu dari: helpdesk, technician, supervisor',
            'department_id.exists' => 'Department yang dipilih tidak valid',
        ];
    }
}
