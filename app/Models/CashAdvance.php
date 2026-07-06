<?php

namespace App\Models;

use App\Models\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CashAdvance extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'rider_id',
        'amount',
        'date',
        'notes',
        'is_deducted',
        'admin_id',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'date'        => 'date',
        'is_deducted' => 'boolean',
    ];

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function payslips(): BelongsToMany
    {
        return $this->belongsToMany(Payslip::class, 'payslip_cash_advances');
    }
}
