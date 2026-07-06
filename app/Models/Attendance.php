<?php

namespace App\Models;

use App\Models\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'rider_id',
        'spx_account_id',
        'date',
        'status',
        'notes',
        'admin_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function spxAccount(): BelongsTo
    {
        return $this->belongsTo(SpxAccount::class);
    }

    public function getDayValueAttribute(): float
    {
        return match($this->status) {
            'present' => 1.0,
            default   => 0.0,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'present'  => 'Present',
            'absent'   => 'Absent',
            'rest_day' => 'Rest Day',
            'half_day' => 'Half Day',
            default    => ucfirst($this->status),
        };
    }
}
