<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class TicketActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('ticket.change_status');
    }

    public function rules(): array
    {
        return [
            'rejection_reason' => 'nullable|string|min:10|max:1000',
            'unresolve_reason' => 'nullable|string|min:10|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'rejection_reason.min' => 'Alasan penolakan minimal 10 karakter',
            'rejection_reason.max' => 'Alasan penolakan maksimal 1000 karakter',
            'unresolve_reason.min' => 'Alasan pembatalan minimal 10 karakter',
            'unresolve_reason.max' => 'Alasan pembatalan maksimal 1000 karakter',
        ];
    }
}
