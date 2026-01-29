<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechnicianTicketHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'technician_id',
        'resolved_at',
        'solution_text',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /* ================= RELATIONS ================= */

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
