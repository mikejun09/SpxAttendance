<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Rider;
use App\Models\Attendance;
use App\Models\Payslip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AbsentRiderPayslipTest extends TestCase
{
    use RefreshDatabase;

    public function test_individual_payslip_fails_for_absent_rider(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $rider = Rider::create([
            'name' => 'Absent Rider Test',
            'daily_rate' => 500.00,
            'is_active' => true,
            'admin_id' => $admin->id,
        ]);

        $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);
        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Record absences all week
        for ($i = 0; $i < 7; $i++) {
            Attendance::create([
                'rider_id' => $rider->id,
                'date' => $weekStart->copy()->addDays($i)->toDateString(),
                'status' => 'absent',
                'admin_id' => $admin->id,
            ]);
        }

        // Submitting individual payslip generation should fail
        $response = $this
            ->actingAs($admin)
            ->from(route('payslips.create', ['rider_id' => $rider->id, 'week_start' => $weekStart->toDateString()]))
            ->post(route('payslips.store'), [
                'rider_id' => $rider->id,
                'week_start' => $weekStart->toDateString(),
                'status' => 'final',
            ]);

        $response->assertRedirect(route('payslips.create', ['rider_id' => $rider->id, 'week_start' => $weekStart->toDateString()]));
        $response->assertSessionHasErrors('week_start');
        
        $this->assertEquals(0, Payslip::count());
    }

    public function test_bulk_payslip_excludes_absent_rider(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $rider1 = Rider::create([
            'name' => 'Present Rider',
            'daily_rate' => 500.00,
            'is_active' => true,
            'admin_id' => $admin->id,
        ]);
        $rider2 = Rider::create([
            'name' => 'Absent Rider',
            'daily_rate' => 500.00,
            'is_active' => true,
            'admin_id' => $admin->id,
        ]);

        $weekStart = Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY);

        // Rider 1: Present
        Attendance::create([
            'rider_id' => $rider1->id,
            'date' => $weekStart->toDateString(),
            'status' => 'present',
            'admin_id' => $admin->id,
        ]);

        // Rider 2: Absent all week
        for ($i = 0; $i < 7; $i++) {
            Attendance::create([
                'rider_id' => $rider2->id,
                'date' => $weekStart->copy()->addDays($i)->toDateString(),
                'status' => 'absent',
                'admin_id' => $admin->id,
            ]);
        }

        // Get bulk preview
        $response = $this
            ->actingAs($admin)
            ->get(route('payslips.bulk-cutoff', ['week_start' => $weekStart->toDateString()]));

        $response->assertOk();
        $response->assertSee('Present Rider');
        $response->assertDontSee('Absent Rider');

        // Post bulk generation with both rider IDs (e.g. attempting to force it)
        $responseStore = $this
            ->actingAs($admin)
            ->post(route('payslips.bulk-cutoff.store'), [
                'week_start' => $weekStart->toDateString(),
                'status' => 'final',
                'rider_ids' => [$rider1->id, $rider2->id],
            ]);

        $responseStore->assertRedirect();
        
        // Payslip should be generated for Rider 1, but NOT Rider 2
        $this->assertEquals(1, Payslip::count());
        $this->assertEquals($rider1->id, Payslip::first()->rider_id);
    }
}
