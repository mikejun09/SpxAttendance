<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payslip_additions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payslip_id')->constrained('payslips')->cascadeOnDelete();
            $table->string('label', 100);          // e.g. "Weekly Incentive", "Overtime", "Gas Allowance"
            $table->decimal('amount', 10, 2);
            $table->timestamps();
        });

        Schema::table('payslips', function (Blueprint $table) {
            $table->decimal('additional_pay', 10, 2)->default(0)->after('gross_pay');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payslip_additions');
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn('additional_pay');
        });
    }
};
