<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Rider;
use App\Models\SpxAccount;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $adminUser = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@spx.com',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
        ]);

        // SPX Accounts
        $spx1 = SpxAccount::create([
            'account_code' => 'SPX-001',
            'account_name' => 'Quezon City Hub',
            'is_active'    => true,
            'admin_id'     => $adminUser->id,
        ]);

        $spx2 = SpxAccount::create([
            'account_code' => 'SPX-002',
            'account_name' => 'Makati Hub',
            'is_active'    => true,
            'admin_id'     => $adminUser->id,
        ]);

        // Rider users + profiles
        $riderUser1 = User::create([
            'name'     => 'Juan dela Cruz',
            'email'    => 'juan@spx.com',
            'password' => Hash::make('rider123'),
            'role'     => 'rider',
        ]);

        $rider1 = Rider::create([
            'name'           => 'Juan dela Cruz',
            'employee_id'    => 'R-0001',
            'contact_number' => '09171234567',
            'daily_rate'     => 500.00,
            'is_active'      => true,
            'user_id'        => $riderUser1->id,
            'spx_account_id' => $spx1->id,
            'admin_id'       => $adminUser->id,
        ]);

        $riderUser2 = User::create([
            'name'     => 'Pedro Santos',
            'email'    => 'pedro@spx.com',
            'password' => Hash::make('rider123'),
            'role'     => 'rider',
        ]);

        $rider2 = Rider::create([
            'name'           => 'Pedro Santos',
            'employee_id'    => 'R-0002',
            'contact_number' => '09187654321',
            'daily_rate'     => 550.00,
            'is_active'      => true,
            'user_id'        => $riderUser2->id,
            'spx_account_id' => $spx2->id,
            'admin_id'       => $adminUser->id,
        ]);

        // Attendance records for this week (Mon–today)
        $weekStart = now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $today = now()->toDateString();

        for ($i = 0; $i < 5; $i++) {
            $date = $weekStart->copy()->addDays($i)->toDateString();
            if ($date > $today) break;

            Attendance::create([
                'rider_id'       => $rider1->id,
                'spx_account_id' => $spx1->id,
                'date'           => $date,
                'status'         => $i === 2 ? 'half_day' : 'present',
                'admin_id'       => $adminUser->id,
            ]);

            Attendance::create([
                'rider_id'       => $rider2->id,
                'spx_account_id' => $spx2->id,
                'date'           => $date,
                'status'         => $i === 1 ? 'absent' : 'present',
                'admin_id'       => $adminUser->id,
            ]);
        }

        // Cash advance for rider1
        CashAdvance::create([
            'rider_id'    => $rider1->id,
            'amount'      => 300.00,
            'date'        => $weekStart->toDateString(),
            'notes'       => 'Gas allowance advance',
            'is_deducted' => false,
            'admin_id'    => $adminUser->id,
        ]);

        // Miscellaneous Expenses
        \App\Models\Expense::create([
            'amount' => 1250.00,
            'date' => now()->toDateString(),
            'description' => 'Office internet & utility bill',
            'admin_id' => $adminUser->id,
        ]);

        \App\Models\Expense::create([
            'amount' => 450.00,
            'date' => now()->subDays(2)->toDateString(),
            'description' => 'Office cleaning supplies',
            'admin_id' => $adminUser->id,
        ]);

        \App\Models\Expense::create([
            'amount' => 3200.00,
            'date' => now()->startOfMonth()->toDateString(),
            'description' => 'Office rent contribution',
            'admin_id' => $adminUser->id,
        ]);

        // Weekly Incomes
        \App\Models\WeeklyIncome::create([
            'amount' => 15000.00,
            'week_start' => now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString(),
            'notes' => 'Hub delivery payout - Week 1',
            'admin_id' => $adminUser->id,
        ]);

        \App\Models\WeeklyIncome::create([
            'amount' => 18500.00,
            'week_start' => now()->subWeek()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString(),
            'notes' => 'Hub delivery payout - Previous Week',
            'admin_id' => $adminUser->id,
        ]);
    }
}
