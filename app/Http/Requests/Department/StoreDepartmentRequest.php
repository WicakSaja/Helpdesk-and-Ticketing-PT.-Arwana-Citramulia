<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->hasAnyRole('master-admin', 'helpdesk');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:departments,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama departemen wajib diisi',
            'name.unique' => 'Nama departemen sudah ada',
            'name.max' => 'Nama departemen maksimal 255 karakter',
        ];
    }
}
