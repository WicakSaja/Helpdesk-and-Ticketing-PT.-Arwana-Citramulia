<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'requester_id',
        'status_id',
        'subject',
        'description',
        'channel',
        'closed_at',
        'category_id'
    ];

    protected static function booted()
    {
        static::creating(function ($ticket) {
            $ticket->ticket_number = Str::uuid();
        });
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function status()
    {
        return $this->belongsTo(TicketStatus::class);
    }

    public function assignments()
    {
        return $this->hasMany(TicketAssignment::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function solution()
    {
        return $this->hasOne(TicketSolution::class);
    }

    public function logs()
    {
        return $this->hasMany(TicketLog::class);
    }

    public function survey()
    {
        return $this->hasOne(SatisfactionSurvey::class);
    }
}

