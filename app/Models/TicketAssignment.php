<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketAssignment extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'assigned_to',
        'assigned_by',
        'assigned_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
