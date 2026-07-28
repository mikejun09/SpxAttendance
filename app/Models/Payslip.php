<?php

namespace App\Models;

use App\Models\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payslip extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'rider_id',
        'week_start',
        'week_end',
        'days_worked',
        'half_days',
        'daily_rate',
        'gross_pay',
        'additional_pay',
        'cash_advance_deduction',
        'manual_deduction',
        'prior_balance_deduction',
        'net_pay',
        'notes',
        'status',
        'admin_id',
    ];

    protected $casts = [
        'week_start'               => 'date',
        'week_end'                 => 'date',
        'daily_rate'               => 'decimal:2',
        'gross_pay'                => 'decimal:2',
        'additional_pay'           => 'decimal:2',
        'cash_advance_deduction'   => 'decimal:2',
        'manual_deduction'         => 'decimal:2',
        'prior_balance_deduction'  => 'decimal:2',
        'net_pay'                  => 'decimal:2',
    ];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function cashAdvances(): BelongsToMany
    {
        return $this->belongsToMany(CashAdvance::class, 'payslip_cash_advances');
    }

    public function deductions(): HasMany
    {
        return $this->hasMany(PayslipDeduction::class)->orderBy('id');
    }

    public function additions(): HasMany
    {
        return $this->hasMany(PayslipAddition::class)->orderBy('id');
    }

    public function getPeriodLabelAttribute(): string
    {
        return $this->week_start->format('M d') . ' – ' . $this->week_end->format('M d, Y');
    }

    public function getIsFinalAttribute(): bool
    {
        return $this->status === 'final';
    }
}
