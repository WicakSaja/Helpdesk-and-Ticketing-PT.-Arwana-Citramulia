<?php

namespace App\Http\Requests\Ticket;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermissionTo('ticket.create');
    }

    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'category_id' => 'required|exists:categories,id',
            'channel' => 'required|in:web,mobile,email',
        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'Subjek tiket wajib diisi',
            'subject.max' => 'Subjek tiket maksimal 255 karakter',
            'description.required' => 'Deskripsi tiket wajib diisi',
            'description.min' => 'Deskripsi tiket minimal 10 karakter',
            'category_id.required' => 'Kategori tiket wajib dipilih',
            'category_id.exists' => 'Kategori tiket tidak ditemukan',
            'channel.required' => 'Channel tiket wajib dipilih',
            'channel.in' => 'Channel tiket harus web, mobile, atau email',
        ];
    }
}
