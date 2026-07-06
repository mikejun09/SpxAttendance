<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rider;
use App\Models\Payslip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_payroll_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this
            ->actingAs($admin)
            ->get(route('payslips.report'));

        $response->assertOk();
    }

    public function test_non_admin_cannot_access_payroll_report(): void
    {
        $user = User::factory()->create(['role' => 'rider']);

        $response = $this
            ->actingAs($user)
            ->get(route('payslips.report'));

        $response->assertStatus(403);
    }

    public function test_admin_can_filter_report_by_week_start(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $rider = Rider::create([
            'name' => 'Juan dela Cruz',
            'daily_rate' => 500.00,
            'is_active' => true,
        ]);

        $weekStart1 = now()->subWeeks(2)->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
        $weekStart2 = now()->subWeek()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();

        $payslip = Payslip::create([
            'rider_id' => $rider->id,
            'week_start' => $weekStart1,
            'week_end' => now()->subWeeks(2)->endOfWeek(\Carbon\Carbon::SUNDAY)->toDateString(),
            'days_worked' => 5,
            'half_days' => 0,
            'daily_rate' => 500.00,
            'gross_pay' => 2500.00,
            'cash_advance_deduction' => 0,
            'manual_deduction' => 0,
            'net_pay' => 2500.00,
            'status' => 'draft',
        ]);

        // Accessing with week_start filter
        $response = $this
            ->actingAs($admin)
            ->get(route('payslips.report', ['week_start' => $weekStart1]));

        $response->assertOk();
        $response->assertSee($rider->name);

        // Accessing other week - should be empty
        $response2 = $this
            ->actingAs($admin)
            ->get(route('payslips.report', ['week_start' => $weekStart2]));

        $response2->assertOk();
        $response2->assertDontSee($rider->name);
    }

    public function test_admin_can_filter_report_by_custom_week_start(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $rider = Rider::create([
            'name' => 'Juan dela Cruz',
            'daily_rate' => 500.00,
            'is_active' => true,
        ]);

        // A custom start date (say, a Wednesday in the target week)
        $targetWeekStart = now()->subWeeks(2)->startOfWeek(\Carbon\Carbon::MONDAY);
        $customDate = $targetWeekStart->copy()->addDays(2)->toDateString(); // Wednesday

        $payslip = Payslip::create([
            'rider_id' => $rider->id,
            'week_start' => $targetWeekStart->toDateString(),
            'week_end' => $targetWeekStart->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->toDateString(),
            'days_worked' => 5,
            'half_days' => 0,
            'daily_rate' => 500.00,
            'gross_pay' => 2500.00,
            'cash_advance_deduction' => 0,
            'manual_deduction' => 0,
            'net_pay' => 2500.00,
            'status' => 'draft',
        ]);

        // Accessing with custom_week_start date (which should automatically resolve to its Monday)
        $response = $this
            ->actingAs($admin)
            ->get(route('payslips.report', ['custom_week_start' => $customDate]));

        $response->assertOk();
        $response->assertSee($rider->name);
    }
}
