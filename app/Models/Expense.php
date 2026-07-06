<?php

namespace App\Models;

use App\Models\Traits\TenantScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory, TenantScoped;

    protected $fillable = [
        'amount',
        'date',
        'description',
        'admin_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date',
    ];
}
