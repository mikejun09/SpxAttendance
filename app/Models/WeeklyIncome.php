<?php

namespace App\Models;

use App\Models\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyIncome extends Model
{
    use HasFactory, TenantScoped;

    protected $table = 'weekly_incomes';

    protected $fillable = [
        'amount',
        'week_start',
        'notes',
        'admin_id',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'week_start' => 'date',
    ];

    public function getPeriodLabelAttribute(): string
    {
        return $this->week_start->format('M d') . ' – ' . $this->week_start->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('M d, Y');
    }
}
