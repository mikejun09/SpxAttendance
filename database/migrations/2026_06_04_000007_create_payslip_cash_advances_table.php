<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslip_cash_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payslip_id')->constrained('payslips')->cascadeOnDelete();
            $table->foreignId('cash_advance_id')->constrained('cash_advances')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['payslip_id', 'cash_advance_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslip_cash_advances');
    }
};
