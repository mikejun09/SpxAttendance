<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rider;
use App\Models\SpxAccount;
use App\Models\Attendance;
use App\Http\Controllers\PayslipController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class DailyRateAdjustmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private SpxAccount $spxAccount;
    private Rider $riderA;
    private Rider $riderB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        
        // Log in to automatically trigger TenantScoped trait scoping if needed
        $this->actingAs($this->admin);

        $this->spxAccount = SpxAccount::create([
            'account_code' => 'SPX-TEST',
            'account_name' => 'Test Hub',
            'is_active' => true,
        ]);

        $this->riderA = Rider::create([
            'name' => 'Rider A',
            'daily_rate' => 500.00,
            'is_active' => true,
            'spx_account_id' => $this->spxAccount->id,
        ]);

        $this->riderB = Rider::create([
            'name' => 'Rider B',
            'daily_rate' => 500.00,
            'is_active' => true,
            'spx_account_id' => $this->spxAccount->id,
        ]);
    }

    /**
     * Helper to invoke the private computePayslip method using reflection.
     */
    private function calculate(Rider $rider, Carbon $weekStart, Carbon $weekEnd): array
    {
        $controller = new PayslipController();
        $reflector = new \ReflectionMethod(PayslipController::class, 'computePayslip');
        $reflector->setAccessible(true);
        return $reflector->invoke($controller, $rider, $weekStart, $weekEnd);
    }

    /**
     * Test rate is adjusted to 100 when only one rider is present at the SPX hub on that day.
     */
    public function test_rate_becomes_100_when_single_rider_present(): void
    {
        $date = Carbon::parse('2026-07-06'); // A Monday
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $date->copy()->endOfWeek(Carbon::SUNDAY);

        // Only Rider A is present
        Attendance::create([
            'rider_id' => $this->riderA->id,
            'spx_account_id' => $this->spxAccount->id,
            'date' => $date->toDateString(),
            'status' => 'present',
        ]);

        $result = $this->calculate($this->riderA, $weekStart, $weekEnd);

        $this->assertEquals(1, $result['days_worked']);
        $this->assertEquals(1.0, $result['total_days']);
        // Gross pay should be 100 instead of the rider's default 500
        $this->assertEquals(100.00, $result['gross_pay']);
    }

    /**
     * Test rate remains default when multiple riders are present at the same hub on the same day.
     */
    public function test_rate_remains_default_when_multiple_riders_present(): void
    {
        $date = Carbon::parse('2026-07-06'); // A Monday
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $date->copy()->endOfWeek(Carbon::SUNDAY);

        // Both Rider A and Rider B are present
        Attendance::create([
            'rider_id' => $this->riderA->id,
            'spx_account_id' => $this->spxAccount->id,
            'date' => $date->toDateString(),
            'status' => 'present',
        ]);

        Attendance::create([
            'rider_id' => $this->riderB->id,
            'spx_account_id' => $this->spxAccount->id,
            'date' => $date->toDateString(),
            'status' => 'present',
        ]);

        $result = $this->calculate($this->riderA, $weekStart, $weekEnd);

        $this->assertEquals(1, $result['days_worked']);
        $this->assertEquals(1.0, $result['total_days']);
        // Gross pay should be 500.00 because there were 2 riders present
        $this->assertEquals(500.00, $result['gross_pay']);
    }

    /**
     * Test half day adjustment calculations.
     */
    public function test_rate_is_adjusted_on_half_days(): void
    {
        $date = Carbon::parse('2026-07-06'); // A Monday
        $weekStart = $date->copy()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $date->copy()->endOfWeek(Carbon::SUNDAY);

        // Rider A is half-day, and only rider present
        Attendance::create([
            'rider_id' => $this->riderA->id,
            'spx_account_id' => $this->spxAccount->id,
            'date' => $date->toDateString(),
            'status' => 'half_day',
        ]);

        $result = $this->calculate($this->riderA, $weekStart, $weekEnd);

        $this->assertEquals(0, $result['days_worked']);
        $this->assertEquals(1, $result['half_days']);
        $this->assertEquals(0.5, $result['total_days']);
        // Gross pay should be 0.5 * 100 = 50.00
        $this->assertEquals(50.00, $result['gross_pay']);
    }
}
