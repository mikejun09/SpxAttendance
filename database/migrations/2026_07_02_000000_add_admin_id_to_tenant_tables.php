<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'spx_accounts',
            'riders',
            'attendances',
            'cash_advances',
            'payslips',
            'expenses',
            'weekly_incomes',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('admin_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('users')
                    ->cascadeOnDelete();
            });
        }

        // Backfill existing records with the first admin user's ID
        $firstAdmin = DB::table('users')->where('role', 'admin')->first();
        if ($firstAdmin) {
            foreach ($tables as $tableName) {
                DB::table($tableName)->update(['admin_id' => $firstAdmin->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'weekly_incomes',
            'expenses',
            'payslips',
            'cash_advances',
            'attendances',
            'riders',
            'spx_accounts',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign([$tableName . '_admin_id_foreign']);
                $table->dropColumn('admin_id');
            });
        }
    }
};
