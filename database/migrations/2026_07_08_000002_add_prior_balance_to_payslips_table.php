<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->decimal('prior_balance_deduction', 10, 2)->default(0)->after('manual_deduction')
                ->comment('Amount of prior carried balance applied/deducted on this payslip');
        });
    }

    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn('prior_balance_deduction');
        });
    }
};
