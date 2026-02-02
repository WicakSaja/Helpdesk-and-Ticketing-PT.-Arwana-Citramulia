<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class SolveTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('ticket.resolve');
    }

    public function rules(): array
    {
        return [
            'solution' => 'required|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'solution.required' => 'Solusi tiket wajib diisi',
            'solution.min' => 'Solusi tiket minimal 10 karakter',
        ];
    }
}
