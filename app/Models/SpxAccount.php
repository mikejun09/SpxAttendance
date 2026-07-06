<?php

namespace App\Models;

use App\Models\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpxAccount extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'account_code',
        'account_name',
        'notes',
        'is_active',
        'admin_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Riders currently assigned (standing assignment) */
    public function riders(): HasMany
    {
        return $this->hasMany(Rider::class);
    }

    /** All attendance records tied to this account */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getTodayRidersCountAttribute(): int
    {
        return $this->attendances()
            ->whereDate('date', today())
            ->whereIn('status', ['present', 'half_day'])
            ->count();
    }

    public function getAssignedRidersCountAttribute(): int
    {
        return $this->riders()->where('is_active', true)->count();
    }
}
