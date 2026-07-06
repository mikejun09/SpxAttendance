<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Payslip;
use App\Models\PayslipDeduction;
use App\Models\Rider;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    public function index(Request $request)
    {
        $query = Payslip::with('rider');

        if ($request->filled('rider_id')) {
            $query->where('rider_id', $request->rider_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Rider users can only see their own payslips
        if (auth()->user()->isRider()) {
            $query->where('rider_id', auth()->user()->rider?->id);
        }

        $payslips = $query->orderBy('week_start', 'desc')->paginate(20)->withQueryString();
        $riders   = Rider::where('is_active', true)->orderBy('name')->get();

        return view('payslips.index', compact('payslips', 'riders'));
    }

    public function create(Request $request)
    {
        $riders = Rider::where('is_active', true)->orderBy('name')->get();

        // Pre-fill week_start to last Monday if not passed
        $weekStart = $request->filled('week_start')
            ? Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY)
            : now()->subWeek()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $selectedRider = $request->filled('rider_id') ? Rider::find($request->rider_id) : null;

        // Preview computation if rider selected
        $preview = null;
        if ($selectedRider) {
            $preview = $this->computePayslip($selectedRider, $weekStart, $weekEnd);
        }

        return view('payslips.create', compact('riders', 'weekStart', 'weekEnd', 'selectedRider', 'preview'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rider_id'                => 'required|exists:riders,id',
            'week_start'              => 'required|date',
            'cash_advance_ids'        => 'nullable|array',
            'cash_advance_ids.*'      => 'exists:cash_advances,id',
            'deductions'              => 'nullable|array',
            'deductions.*.label'      => 'required_with:deductions.*.amount|string|max:100',
            'deductions.*.amount'     => 'required_with:deductions.*.label|numeric|min:0',
            'notes'                   => 'nullable|string',
            'status'                  => 'required|in:draft,final',
        ]);

        $rider = Rider::findOrFail($validated['rider_id']);
        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        // Check if payslip already exists for this rider+week
        $existing = Payslip::where('rider_id', $rider->id)
            ->whereDate('week_start', $weekStart->toDateString())
            ->first();

        if ($existing) {
            return back()->withErrors(['week_start' => 'A payslip already exists for this rider and week.'])->withInput();
        }

        $data = $this->computePayslip($rider, $weekStart, $weekEnd);

        // Determine CA deduction
        $caDeduction = 0;
        $caIds = $validated['cash_advance_ids'] ?? [];
        if (!empty($caIds)) {
            $caDeduction = CashAdvance::whereIn('id', $caIds)
                ->where('rider_id', $rider->id)
                ->where('is_deducted', false)
                ->sum('amount');
        }

        // Determine manual deductions
        $manualDeductions = collect($validated['deductions'] ?? [])
            ->filter(fn($d) => isset($d['label'], $d['amount']) && $d['label'] !== '' && $d['amount'] > 0);
        $manualTotal = $manualDeductions->sum('amount');

        $grossPay = $data['gross_pay'];
        $netPay   = max(0, $grossPay - $caDeduction - $manualTotal);

        $payslip = Payslip::create([
            'rider_id'               => $rider->id,
            'week_start'             => $weekStart->toDateString(),
            'week_end'               => $weekEnd->toDateString(),
            'days_worked'            => $data['days_worked'],
            'half_days'              => $data['half_days'],
            'daily_rate'             => $rider->daily_rate,
            'gross_pay'              => $grossPay,
            'cash_advance_deduction' => $caDeduction,
            'manual_deduction'       => $manualTotal,
            'net_pay'                => $netPay,
            'notes'                  => $validated['notes'] ?? null,
            'status'                 => $validated['status'],
        ]);

        // Attach cash advances and mark as deducted
        if (!empty($caIds)) {
            $payslip->cashAdvances()->attach($caIds);
            CashAdvance::whereIn('id', $caIds)->update(['is_deducted' => true]);
        }

        // Save manual deduction rows
        foreach ($manualDeductions as $d) {
            PayslipDeduction::create([
                'payslip_id' => $payslip->id,
                'label'      => $d['label'],
                'amount'     => $d['amount'],
            ]);
        }

        return redirect()->route('payslips.show', $payslip)
            ->with('success', 'Payslip generated successfully.');
    }

    public function show(Payslip $payslip)
    {
        // Riders can only see their own
        if (auth()->user()->isRider() && auth()->user()->rider?->id !== $payslip->rider_id) {
            abort(403);
        }

        $payslip->load(['rider', 'cashAdvances', 'deductions']);

        $attendances = Attendance::with('spxAccount')
            ->where('rider_id', $payslip->rider_id)
            ->whereBetween('date', [$payslip->week_start, $payslip->week_end])
            ->whereNotIn('status', ['absent', 'rest_day'])
            ->orderBy('date')
            ->get();

        return view('payslips.show', compact('payslip', 'attendances'));
    }

    public function print(Payslip $payslip)
    {
        $payslip->load(['rider', 'cashAdvances', 'deductions']);

        $attendances = Attendance::with('spxAccount')
            ->where('rider_id', $payslip->rider_id)
            ->whereBetween('date', [$payslip->week_start, $payslip->week_end])
            ->whereNotIn('status', ['absent', 'rest_day'])
            ->orderBy('date')
            ->get();

        return view('payslips.print', compact('payslip', 'attendances'));
    }

    public function destroy(Payslip $payslip)
    {
        // Revert cash advance deducted flag
        $payslip->cashAdvances()->update(['is_deducted' => false]);
        $payslip->cashAdvances()->detach();
        // payslip_deductions cascade-delete automatically
        $payslip->delete();

        return redirect()->route('payslips.index')
            ->with('success', 'Payslip deleted and cash advances restored.');
    }

    // ─── Payroll Summary Report ────────────────────────────────────────────────

    public function report(Request $request)
    {
        $dateInput = $request->filled('week_start') 
            ? $request->week_start 
            : ($request->filled('custom_week_start') ? $request->custom_week_start : null);

        $weekStart = $dateInput
            ? Carbon::parse($dateInput)->startOfWeek(Carbon::MONDAY)
            : now()->subWeek()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $payslips = Payslip::with(['rider', 'deductions', 'cashAdvances'])
            ->join('riders', 'payslips.rider_id', '=', 'riders.id')
            ->whereDate('payslips.week_start', $weekStart->toDateString())
            ->orderBy('riders.name')
            ->select('payslips.*')
            ->get();

        $totals = [
            'gross'   => $payslips->sum('gross_pay'),
            'ca'      => $payslips->sum('cash_advance_deduction'),
            'manual'  => $payslips->sum('manual_deduction'),
            'net'     => $payslips->sum('net_pay'),
            'days'    => $payslips->sum('days_worked'),
        ];

        // Available weeks for the selector (distinct week_starts in payslips)
        $weeks = Payslip::selectRaw('week_start, week_end')
            ->distinct()
            ->orderBy('week_start', 'desc')
            ->limit(20)
            ->get();

        return view('payslips.report', compact('payslips', 'weekStart', 'weekEnd', 'totals', 'weeks'));
    }

    // ─── Bulk Weekly Cut-off ───────────────────────────────────────────────────

    public function bulkCutoffForm(Request $request)
    {
        // Default to last completed week (Mon–Sun)
        $weekStart = $request->filled('week_start')
            ? Carbon::parse($request->week_start)->startOfWeek(Carbon::MONDAY)
            : now()->subWeek()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $riders = Rider::where('is_active', true)->orderBy('name')->get();

        // Build preview rows for every active rider
        $preview = $riders->map(function (Rider $rider) use ($weekStart, $weekEnd) {
            $data = $this->computePayslip($rider, $weekStart, $weekEnd);

            $existing = Payslip::where('rider_id', $rider->id)
                ->whereDate('week_start', $weekStart->toDateString())
                ->exists();

            $caTotal = $data['pending_ca']->sum('amount');

            return [
                'rider'       => $rider,
                'days_worked' => $data['days_worked'],
                'half_days'   => $data['half_days'],
                'total_days'  => $data['total_days'],
                'gross_pay'   => $data['gross_pay'],
                'ca_total'    => $caTotal,
                'pending_ca'  => $data['pending_ca'],
                'net_pay'     => max(0, $data['gross_pay'] - $caTotal),
                'existing'    => $existing,
            ];
        });

        return view('payslips.bulk-cutoff', compact('weekStart', 'weekEnd', 'preview'));
    }

    public function bulkCutoffStore(Request $request)
    {
        $validated = $request->validate([
            'week_start'  => 'required|date',
            'status'      => 'required|in:draft,final',
            'rider_ids'   => 'required|array|min:1',
            'rider_ids.*' => 'exists:riders,id',
        ]);

        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek(Carbon::MONDAY);
        $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);

        $generated = 0;
        $skipped   = 0;

        foreach ($validated['rider_ids'] as $riderId) {
            $rider = Rider::find($riderId);
            if (!$rider) { $skipped++; continue; }

            // Skip if payslip already exists
            $existing = Payslip::where('rider_id', $rider->id)
                ->whereDate('week_start', $weekStart->toDateString())
                ->exists();

            if ($existing) { $skipped++; continue; }

            $data = $this->computePayslip($rider, $weekStart, $weekEnd);

            // Auto-deduct ALL pending cash advances
            $pendingCa = CashAdvance::where('rider_id', $rider->id)
                ->where('is_deducted', false)
                ->get();

            $caDeduction = $pendingCa->sum('amount');
            $netPay      = max(0, $data['gross_pay'] - $caDeduction);

            $payslip = Payslip::create([
                'rider_id'               => $rider->id,
                'week_start'             => $weekStart->toDateString(),
                'week_end'               => $weekEnd->toDateString(),
                'days_worked'            => $data['days_worked'],
                'half_days'              => $data['half_days'],
                'daily_rate'             => $rider->daily_rate,
                'gross_pay'              => $data['gross_pay'],
                'cash_advance_deduction' => $caDeduction,
                'manual_deduction'       => 0,
                'net_pay'                => $netPay,
                'notes'                  => null,
                'status'                 => $validated['status'],
            ]);

            if ($pendingCa->isNotEmpty()) {
                $caIds = $pendingCa->pluck('id')->toArray();
                $payslip->cashAdvances()->attach($caIds);
                CashAdvance::whereIn('id', $caIds)->update(['is_deducted' => true]);
            }

            $generated++;
        }

        $msg = "Weekly cut-off complete: {$generated} payslip(s) generated.";
        if ($skipped > 0) $msg .= " {$skipped} rider(s) skipped (payslip already exists or not found).";

        return redirect()->route('payslips.index')
            ->with('success', $msg);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function computePayslip(Rider $rider, Carbon $weekStart, Carbon $weekEnd): array
    {
        $attendances = Attendance::where('rider_id', $rider->id)
            ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->get();

        $daysWorked = 0;
        $halfDays   = 0;
        $grossPay   = 0;

        foreach ($attendances as $att) {
            $status = $att->status;
            if (in_array($status, ['present', 'half_day'])) {
                $rate = $rider->daily_rate;

                if ($att->spx_account_id) {
                    $presentCount = Attendance::where('spx_account_id', $att->spx_account_id)
                        ->where('date', $att->date)
                        ->whereIn('status', ['present', 'half_day'])
                        ->count();

                    if ($presentCount === 1) {
                        $rate = 1000;
                    }
                }

                if ($status === 'present') {
                    $daysWorked += 1;
                    $grossPay += 1.0 * $rate;
                } else {
                    $halfDays += 1;
                    $grossPay += 0.5 * $rate;
                }
            }
        }

        $totalDays = $daysWorked + ($halfDays * 0.5);

        $pendingCa = CashAdvance::where('rider_id', $rider->id)
            ->where('is_deducted', false)
            ->orderBy('date')
            ->get();

        return [
            'days_worked' => $daysWorked,
            'half_days'   => $halfDays,
            'total_days'  => $totalDays,
            'gross_pay'   => $grossPay,
            'pending_ca'  => $pendingCa,
            'attendances' => $attendances->reject(fn($att) => in_array($att->status, ['absent', 'rest_day'])),
        ];
    }
}
