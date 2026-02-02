<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class AssignTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('ticket.assign');
    }

    public function rules(): array
    {
        return [
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'assigned_to.required' => 'Teknisi wajib dipilih',
            'assigned_to.exists' => 'Teknisi tidak ditemukan',
            'notes.max' => 'Catatan maksimal 1000 karakter',
        ];
    }
}
