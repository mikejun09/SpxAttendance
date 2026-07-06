<?php

namespace Tests\Feature;

use App\Models\CashAdvance;
use App\Models\Rider;
use App\Models\User;
use App\Models\Payslip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashAdvanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_pending_cash_advance(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $rider = Rider::create([
            'name' => 'Juan dela Cruz',
            'daily_rate' => 500.00,
            'is_active' => true,
        ]);
        $cashAdvance = CashAdvance::create([
            'rider_id' => $rider->id,
            'amount' => 300.00,
            'date' => now()->toDateString(),
            'is_deducted' => false,
        ]);

        $response = $this
            ->actingAs($admin)
            ->delete(route('cash-advances.destroy', $cashAdvance));

        $response->assertRedirect();
        $this->assertDatabaseMissing('cash_advances', ['id' => $cashAdvance->id]);
    }

    public function test_non_admin_cannot_delete_cash_advance(): void
    {
        $user = User::factory()->create(['role' => 'rider']);
        $rider = Rider::create([
            'name' => 'Juan dela Cruz',
            'daily_rate' => 500.00,
            'is_active' => true,
        ]);
        $cashAdvance = CashAdvance::create([
            'rider_id' => $rider->id,
            'amount' => 300.00,
            'date' => now()->toDateString(),
            'is_deducted' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('cash-advances.destroy', $cashAdvance));

        $response->assertStatus(403);
        $this->assertDatabaseHas('cash_advances', ['id' => $cashAdvance->id]);
    }

    public function test_admin_can_delete_deducted_cash_advance_and_recalculate_payslips(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $rider = Rider::create([
            'name' => 'Juan dela Cruz',
            'daily_rate' => 500.00,
            'is_active' => true,
        ]);
        
        $cashAdvance = CashAdvance::create([
            'rider_id' => $rider->id,
            'amount' => 300.00,
            'date' => now()->toDateString(),
            'is_deducted' => true,
        ]);

        $payslip = Payslip::create([
            'rider_id' => $rider->id,
            'week_start' => now()->startOfWeek()->toDateString(),
            'week_end' => now()->endOfWeek()->toDateString(),
            'days_worked' => 5,
            'half_days' => 0,
            'daily_rate' => 500.00,
            'gross_pay' => 2500.00,
            'cash_advance_deduction' => 300.00,
            'manual_deduction' => 0,
            'net_pay' => 2200.00,
            'status' => 'draft',
        ]);

        $payslip->cashAdvances()->attach($cashAdvance->id);

        $response = $this
            ->actingAs($admin)
            ->from(route('cash-advances.index'))
            ->delete(route('cash-advances.destroy', $cashAdvance));

        $response->assertRedirect(route('cash-advances.index'));
        $this->assertDatabaseMissing('cash_advances', ['id' => $cashAdvance->id]);

        $payslip->refresh();
        $this->assertEquals(0, $payslip->cash_advance_deduction);
        $this->assertEquals(2500.00, $payslip->net_pay);
    }
}
