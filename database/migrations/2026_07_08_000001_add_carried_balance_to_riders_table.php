<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->decimal('carried_balance', 10, 2)->default(0)->after('daily_rate')
                ->comment('Outstanding debt carried forward from previous payslips where deductions exceeded gross pay');
        });
    }

    public function down(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('carried_balance');
        });
    }
};
