<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rider;
use App\Models\Payslip;
use App\Models\WeeklyIncome;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlyFinancialsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Business rule: "Last week's payslip is deducted from this week's income."
     *
     * The June 29 cutoff week spans Jun 29 – Jul 5 (Thursday = Jul 2 → July).
     *   → Income recorded for the week starting Jun 29 belongs to JULY.
     *
     * The June 22 cutoff week spans Jun 22 – Jun 28 (Thursday = Jun 25 → June).
     *   → But this payslip is DEDUCTED from the Jun 29 income (which is July).
     *   → So the Jun 22 payslip salary expense belongs to JULY.
     */
    public function test_income_for_jun29_week_belongs_to_july(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Income recorded for the week starting June 29 (Thursday = July 2 → July)
        WeeklyIncome::create([
            'amount'     => 10000.00,
            'week_start' => '2026-06-29',
            'admin_id'   => $admin->id,
        ]);

        // June: income should be 0
        $responseJune = $this->actingAs($admin)->get(route('dashboard', ['month' => '2026-06', 'period' => 'month']));
        $responseJune->assertStatus(200);
        $this->assertEquals(0, $responseJune->viewData('monthlyFinancials')['income']);

        // July: income should be 10000
        $responseJuly = $this->actingAs($admin)->get(route('dashboard', ['month' => '2026-07', 'period' => 'month']));
        $responseJuly->assertStatus(200);
        $this->assertEquals(10000.00, $responseJuly->viewData('monthlyFinancials')['income']);
    }

    /**
     * The June 22 payslip (Thursday = June 25 → June by majority) is deducted from
     * the June 29 week's income (which belongs to July). Therefore the June 22 payslip
     * salary expense must be counted under JULY, not June.
     */
    public function test_payslip_for_jun22_week_belongs_to_july_not_june(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $rider = Rider::create([
            'name'       => 'Test Rider',
            'daily_rate' => 500.00,
            'is_active'  => true,
            'admin_id'   => $admin->id,
        ]);

        // Payslip for the week starting June 22 (Thursday = June 25 → majority in June).
        // Per business rule this is deducted from the Jun 29 income (July), so it belongs to July.
        Payslip::create([
            'rider_id'   => $rider->id,
            'week_start' => '2026-06-22',
            'week_end'   => '2026-06-28',
            'days_worked' => 5,
            'half_days'  => 0,
            'daily_rate' => 500.00,
            'gross_pay'  => 2500.00,
            'net_pay'    => 2500.00,
            'status'     => 'final',
            'admin_id'   => $admin->id,
        ]);

        // June: gross_salary should be 0 (not counted here — deducted in the next week's income)
        $responseJune = $this->actingAs($admin)->get(route('dashboard', ['month' => '2026-06', 'period' => 'month']));
        $responseJune->assertStatus(200);
        $this->assertEquals(0, $responseJune->viewData('monthlyFinancials')['gross_salary']);

        // July: gross_salary should be 2500 (June 22 payslip deducted from June 29 income)
        $responseJuly = $this->actingAs($admin)->get(route('dashboard', ['month' => '2026-07', 'period' => 'month']));
        $responseJuly->assertStatus(200);
        $this->assertEquals(2500.00, $responseJuly->viewData('monthlyFinancials')['gross_salary']);
    }

    /**
     * Both rules together: June 29 income (→ July) paired with June 22 payslip (→ July).
     */
    public function test_combined_income_and_payslip_both_land_in_july(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $rider = Rider::create([
            'name'       => 'Test Rider',
            'daily_rate' => 500.00,
            'is_active'  => true,
            'admin_id'   => $admin->id,
        ]);

        // June 29 income → July
        WeeklyIncome::create([
            'amount'     => 10000.00,
            'week_start' => '2026-06-29',
            'admin_id'   => $admin->id,
        ]);

        // June 22 payslip → July (deducted from June 29 income)
        Payslip::create([
            'rider_id'    => $rider->id,
            'week_start'  => '2026-06-22',
            'week_end'    => '2026-06-28',
            'days_worked' => 5,
            'half_days'   => 0,
            'daily_rate'  => 500.00,
            'gross_pay'   => 2500.00,
            'net_pay'     => 2500.00,
            'status'      => 'final',
            'admin_id'    => $admin->id,
        ]);

        $responseJuly = $this->actingAs($admin)->get(route('dashboard', ['month' => '2026-07', 'period' => 'month']));
        $responseJuly->assertStatus(200);

        $financials = $responseJuly->viewData('monthlyFinancials');
        $this->assertEquals(10000.00, $financials['income'],       'Income for Jun 29 week should land in July');
        $this->assertEquals(2500.00,  $financials['gross_salary'], 'Salary for Jun 22 payslip should land in July');
        $this->assertEquals(7500.00,  $financials['net_profit'],   'Net profit = 10000 - 2500');
    }
}
