<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_number',
        'requester_id',
        'status_id',
        'subject',
        'description',
        'channel',
        'category_id',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    /* ================= RELATION ================= */

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function assignment()
    {
        return $this->hasOne(TicketAssignment::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function solution()
    {
        return $this->hasOne(TicketSolution::class);
    }

    public function technicianHistories()
    {
        return $this->hasMany(TechnicianTicketHistory::class);
    }
}

