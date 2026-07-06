<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->foreignId('spx_account_id')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('spx_accounts')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropForeign(['spx_account_id']);
            $table->dropColumn('spx_account_id');
        });
    }
};
