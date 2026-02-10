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
            'resolved_at' => 'required|date_format:Y-m-d H:i:s',
        ];
    }

    public function messages(): array
    {
        return [
            'solution.required' => 'Solusi tiket wajib diisi',
            'solution.min' => 'Solusi tiket minimal 10 karakter',
            'resolved_at.required' => 'Tanggal dan waktu selesai wajib diisi',
            'resolved_at.date_format' => 'Format tanggal dan waktu selesai tidak valid',
        ];
    }
}
