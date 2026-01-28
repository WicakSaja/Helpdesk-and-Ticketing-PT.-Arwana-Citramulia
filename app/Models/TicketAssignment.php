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
        'assigned_at',
        'notes'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
    
    public function technician()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
