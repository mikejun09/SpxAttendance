<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Payslip;
use App\Models\Rider;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NegativeBalanceCarryoverTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function makeRider(User $admin, float $rate = 500.00): Rider
    {
        return Rider::create([
            'name'             => 'Test Rider',
            'daily_rate'       => $rate,
            'is_active'        => true,
            'admin_id'         => $admin->id,
            'carried_balance'  => 0,
        ]);
    }

    private function markPresent(Rider $rider, Carbon $date, User $admin): void
    {
        Attendance::create([
            'rider_id' => $rider->id,
            'date'     => $date->toDateString(),
            'status'   => 'present',
            'admin_id' => $admin->id,
        ]);
    }

    /**
     * Scenario A: Deductions exceed gross — carried_balance is set, net_pay = 0.
     */
    public function test_deductions_exceeding_gross_sets_carried_balance_and_zeroes_net_pay(): void
    {
        $admin  = $this->makeAdmin();
        $rider  = $this->makeRider($admin, 500.00); // ₱500/day

        // Week 1: 1 day present → gross = ₱500
        $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $this->markPresent($rider, $weekStart, $admin);

        // Create a cash advance of ₱700 — more than gross
        $ca = CashAdvance::create([
            'rider_id'   => $rider->id,
            'amount'     => 700.00,
            'date'       => $weekStart->toDateString(),
            'is_deducted'=> false,
            'admin_id'   => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post(route('payslips.store'), [
            'rider_id'         => $rider->id,
            'week_start'       => $weekStart->toDateString(),
            'cash_advance_ids' => [$ca->id],
            'status'           => 'final',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $payslip = Payslip::first();
        $this->assertNotNull($payslip);
        $this->assertEquals(0, (float) $payslip->net_pay);
        $this->assertEquals(0, (float) $payslip->prior_balance_deduction); // No prior balance existed
        $this->assertEquals(500.00, (float) $payslip->gross_pay);
        $this->assertEquals(700.00, (float) $payslip->cash_advance_deduction);

        // Rider should now have a carried_balance of ₱200 (₱700 - ₱500)
        $rider->refresh();
        $this->assertEquals(200.00, (float) $rider->carried_balance);
    }

    /**
     * Scenario B: Next payslip applies carried balance, rider's carried_balance resets.
     */
    public function test_next_payslip_applies_prior_balance_and_resets_carried_balance(): void
    {
        $admin = $this->makeAdmin();
        // Set rider with existing carried balance of ₱200
        $rider = $this->makeRider($admin, 500.00);
        $rider->update(['carried_balance' => 200.00]);

        $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Week 2: 2 days present → gross = ₱1000
        $this->markPresent($rider, $weekStart, $admin);
        $this->markPresent($rider, $weekStart->copy()->addDay(), $admin);

        $response = $this->actingAs($admin)->post(route('payslips.store'), [
            'rider_id'   => $rider->id,
            'week_start' => $weekStart->toDateString(),
            'status'     => 'final',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $payslip = Payslip::first();
        $this->assertNotNull($payslip);

        // Prior balance of ₱200 should be applied
        $this->assertEquals(200.00, (float) $payslip->prior_balance_deduction);

        // Net pay = ₱1000 - ₱200 = ₱800
        $this->assertEquals(800.00, (float) $payslip->net_pay);

        // Rider's carried_balance should be cleared
        $rider->refresh();
        $this->assertEquals(0.00, (float) $rider->carried_balance);
    }

    /**
     * Scenario C: Deleting a payslip restores prior_balance_deduction to rider's carried_balance.
     */
    public function test_deleting_payslip_restores_prior_balance_to_rider(): void
    {
        $admin = $this->makeAdmin();
        $rider = $this->makeRider($admin, 500.00);
        $rider->update(['carried_balance' => 0]);

        $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $this->markPresent($rider, $weekStart, $admin);

        // Manually create a payslip that absorbed ₱150 of prior balance
        $payslip = Payslip::create([
            'rider_id'               => $rider->id,
            'week_start'             => $weekStart->toDateString(),
            'week_end'               => $weekEnd->toDateString(),
            'days_worked'            => 1,
            'half_days'              => 0,
            'daily_rate'             => 500.00,
            'gross_pay'              => 500.00,
            'cash_advance_deduction' => 0,
            'manual_deduction'       => 0,
            'prior_balance_deduction'=> 150.00,
            'net_pay'                => 350.00,
            'status'                 => 'final',
            'admin_id'               => $admin->id,
        ]);

        // Delete the payslip
        $response = $this->actingAs($admin)->delete(route('payslips.destroy', $payslip));
        $response->assertRedirect(route('payslips.index'));

        // Rider's carried_balance should be restored by ₱150
        $rider->refresh();
        $this->assertEquals(150.00, (float) $rider->carried_balance);
        $this->assertEquals(0, Payslip::count());
    }

    /**
     * Scenario D: Partial recovery — balance too large for one payslip, remainder carries forward again.
     */
    public function test_partial_recovery_carries_remaining_balance_forward(): void
    {
        $admin = $this->makeAdmin();
        $rider = $this->makeRider($admin, 500.00);
        // Rider owes ₱600 from a prior week
        $rider->update(['carried_balance' => 600.00]);

        $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Only 1 day this week → gross = ₱500 — not enough to cover the ₱600 carried balance
        $this->markPresent($rider, $weekStart, $admin);

        $response = $this->actingAs($admin)->post(route('payslips.store'), [
            'rider_id'   => $rider->id,
            'week_start' => $weekStart->toDateString(),
            'status'     => 'final',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();

        $payslip = Payslip::first();

        // Net pay must be ₱0 (can't go negative)
        $this->assertEquals(0.00, (float) $payslip->net_pay);

        // ₱500 was recovered from the ₱600 debt
        $this->assertEquals(500.00, (float) $payslip->prior_balance_deduction);

        // Remaining ₱100 should still be on the rider
        $rider->refresh();
        $this->assertEquals(100.00, (float) $rider->carried_balance);
    }

    /**
     * Scenario E: Bulk cut-off also applies carried_balance correctly.
     */
    public function test_bulk_cutoff_applies_carried_balance(): void
    {
        $admin = $this->makeAdmin();
        $rider = $this->makeRider($admin, 500.00);
        $rider->update(['carried_balance' => 300.00]);

        $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);

        // 3 days present → gross = ₱1500
        for ($i = 0; $i < 3; $i++) {
            $this->markPresent($rider, $weekStart->copy()->addDays($i), $admin);
        }

        $response = $this->actingAs($admin)->post(route('payslips.bulk-cutoff.store'), [
            'week_start' => $weekStart->toDateString(),
            'status'     => 'final',
            'rider_ids'  => [$rider->id],
        ]);

        $response->assertRedirect();

        $payslip = Payslip::where('rider_id', $rider->id)->first();
        $this->assertNotNull($payslip);

        // Prior balance of ₱300 applied
        $this->assertEquals(300.00, (float) $payslip->prior_balance_deduction);

        // Net = ₱1500 - ₱300 = ₱1200
        $this->assertEquals(1200.00, (float) $payslip->net_pay);

        // carried_balance cleared
        $rider->refresh();
        $this->assertEquals(0.00, (float) $rider->carried_balance);
    }

    /**
     * Scenario F: Admin can manually clear outstanding carried balance of a rider.
     */
    public function test_admin_can_clear_rider_carried_balance(): void
    {
        $admin = $this->makeAdmin();
        $rider = $this->makeRider($admin, 500.00);
        $rider->update(['carried_balance' => 450.00]);

        $response = $this->actingAs($admin)->post(route('financials.clear_balance', $rider));
        $response->assertRedirect(route('financials.index', ['active_tab' => 'balances']));

        $rider->refresh();
        $this->assertEquals(0.00, (float) $rider->carried_balance);
    }
}
