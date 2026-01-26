<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketSolution extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'solved_by',
        'solution_text',
        'solved_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
