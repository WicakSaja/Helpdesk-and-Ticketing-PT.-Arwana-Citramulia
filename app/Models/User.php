<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'department_id',
        'is_active'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // ========================
    // RELATIONS
    // ========================

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // Ticket yang dibuat user
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    // Ticket yang ditangani
    public function assignedTickets()
    {
        return $this->hasMany(TicketAssignment::class, 'assigned_to');
    }

    // History penyelesaian ticket oleh technician
    public function resolvedTicketHistories()
    {
        return $this->hasMany(TechnicianTicketHistory::class, 'technician_id');
    }

    // ========================
    // SCOPES
    // ========================

    /**
     * Scope query untuk user yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query untuk user yang non-aktif
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // ========================
    // ACCESSORS & METHODS
    // ========================

    /**
     * Check if user is active
     */
    public function isActive(): bool
    {
        return $this->is_active === true;
    }

    /**
     * Activate user
     */
    public function activate()
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Deactivate user
     */
    public function deactivate()
    {
        $this->is_active = false;
        return $this->save();
    }

}
