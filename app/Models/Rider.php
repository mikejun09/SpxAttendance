<?php

namespace App\Models;

use App\Models\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rider extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'name',
        'employee_id',
        'contact_number',
        'daily_rate',
        'is_active',
        'user_id',
        'spx_account_id',
        'admin_id',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function spxAccount(): BelongsTo
    {
        return $this->belongsTo(SpxAccount::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class)->orderBy('date', 'desc');
    }

    public function cashAdvances(): HasMany
    {
        return $this->hasMany(CashAdvance::class)->orderBy('date', 'desc');
    }

    public function payslips(): HasMany
    {
        return $this->hasMany(Payslip::class)->orderBy('week_start', 'desc');
    }

    public function pendingCashAdvances(): HasMany
    {
        return $this->hasMany(CashAdvance::class)->where('is_deducted', false);
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'active' : 'inactive';
    }
}
