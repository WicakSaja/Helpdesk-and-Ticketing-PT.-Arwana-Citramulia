<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SatisfactionSurvey extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ticket_id',
        'rating',
        'feedback',
        'submitted_at'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}

