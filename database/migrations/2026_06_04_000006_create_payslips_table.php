<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('riders')->cascadeOnDelete();
            $table->date('week_start'); // Monday
            $table->date('week_end');   // Sunday
            $table->integer('days_worked')->default(0);
            $table->integer('half_days')->default(0);
            $table->decimal('daily_rate', 10, 2);
            $table->decimal('gross_pay', 10, 2)->default(0);
            $table->decimal('cash_advance_deduction', 10, 2)->default(0);
            $table->decimal('net_pay', 10, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
