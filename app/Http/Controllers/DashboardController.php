<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\CashAdvance;
use App\Models\Payslip;
use App\Models\Rider;
use App\Models\SpxAccount;
use App\Models\Expense;
use App\Models\WeeklyIncome;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = now()->toDateString();

        $selectedWeekDate = $request->input('week_date', $today);
        try {
            $weekCarbon = \Carbon\Carbon::parse($selectedWeekDate);
        } catch (\Exception $e) {
            $weekCarbon = now();
        }
        $weekStart = $weekCarbon->copy()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();
        $weekEnd = $weekCarbon->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->toDateString();

        $selectedMonth = $request->input('month', now()->format('Y-m'));
        try {
            $monthCarbon = \Carbon\Carbon::parse($selectedMonth . '-01');
        } catch (\Exception $e) {
            $monthCarbon = now();
        }
        $monthStart = $monthCarbon->copy()->startOfMonth()->toDateString();
        $monthEnd = $monthCarbon->copy()->endOfMonth()->toDateString();

        $activePeriod = $request->input('period', 'week');

        $stats = [
            'total_riders'       => Rider::where('is_active', true)->count(),
            'total_spx_accounts' => SpxAccount::where('is_active', true)->count(),
            'present_today'      => Attendance::whereDate('date', $today)
                                              ->whereIn('status', ['present', 'half_day'])
                                              ->count(),
            'absent_today'       => Attendance::whereDate('date', $today)
                                              ->where('status', 'absent')
                                              ->count(),
            'pending_ca'         => CashAdvance::where('is_deducted', false)->sum('amount'),
            'payslips_this_week' => Payslip::where('week_start', '>=', $weekStart)
                                           ->where('week_end', '<=', $weekEnd)
                                           ->count(),
        ];

        // ── Financial Calculations ───────────────────────────────────────────
        
        // Weekly Calculations
        $weeklyFinancials = [
            'misc_expenses' => Expense::whereBetween('date', [$weekStart, $weekEnd])->sum('amount'),
            'cash_advances' => CashAdvance::whereBetween('date', [$weekStart, $weekEnd])->sum('amount'),
            'gross_salary'  => Payslip::whereBetween('week_start', [$weekStart, $weekEnd])->sum('gross_pay'),
            'net_salary'    => Payslip::whereBetween('week_start', [$weekStart, $weekEnd])->sum('net_pay'),
            'deductions'    => Payslip::whereBetween('week_start', [$weekStart, $weekEnd])->sum('manual_deduction'),
            'ca_deductions' => Payslip::whereBetween('week_start', [$weekStart, $weekEnd])->sum('cash_advance_deduction'),
            'income'        => WeeklyIncome::whereBetween('week_start', [$weekStart, $weekEnd])->sum('amount'),
        ];

        // Total Expenses = Misc Expenses + Cash Advances Released + Net Rider Salary + Salary Deductions (Manual)
        // (Rider Net Salary + Deductions + CA Deductions = Rider Gross Salary, so this avoids double counting CA)
        $weeklyFinancials['total_expenses'] = $weeklyFinancials['misc_expenses']
                                            + $weeklyFinancials['cash_advances']
                                            + $weeklyFinancials['net_salary']
                                            + $weeklyFinancials['deductions'];

        $weeklyFinancials['net_profit'] = $weeklyFinancials['income'] - $weeklyFinancials['total_expenses'];

        // Monthly Calculations
        $monthlyFinancials = [
            'misc_expenses' => Expense::whereBetween('date', [$monthStart, $monthEnd])->sum('amount'),
            'cash_advances' => CashAdvance::whereBetween('date', [$monthStart, $monthEnd])->sum('amount'),
            'gross_salary'  => Payslip::whereBetween('week_start', [$monthStart, $monthEnd])->sum('gross_pay'),
            'net_salary'    => Payslip::whereBetween('week_start', [$monthStart, $monthEnd])->sum('net_pay'),
            'deductions'    => Payslip::whereBetween('week_start', [$monthStart, $monthEnd])->sum('manual_deduction'),
            'ca_deductions' => Payslip::whereBetween('week_start', [$monthStart, $monthEnd])->sum('cash_advance_deduction'),
            'income'        => WeeklyIncome::whereBetween('week_start', [$monthStart, $monthEnd])->sum('amount'),
        ];

        $monthlyFinancials['total_expenses'] = $monthlyFinancials['misc_expenses']
                                             + $monthlyFinancials['cash_advances']
                                             + $monthlyFinancials['net_salary']
                                             + $monthlyFinancials['deductions'];

        $monthlyFinancials['net_profit'] = $monthlyFinancials['income'] - $monthlyFinancials['total_expenses'];

        // ─────────────────────────────────────────────────────────────────────

        $todayAttendance = Attendance::with(['rider', 'spxAccount'])
            ->whereDate('date', $today)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $recentPayslips = Payslip::with('rider')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $riders     = Rider::with('spxAccount')->where('is_active', true)->orderBy('name')->get();
        $spxAccounts = \App\Models\SpxAccount::where('is_active', true)->orderBy('account_code')->get();

        if (auth()->user()->isRider()) {
            $rider = auth()->user()->rider;
            return redirect()->route('rider.dashboard', $rider);
        }

        return view('dashboard.index', compact(
            'stats', 
            'todayAttendance', 
            'recentPayslips', 
            'riders', 
            'spxAccounts', 
            'weeklyFinancials', 
            'monthlyFinancials',
            'selectedWeekDate',
            'selectedMonth',
            'activePeriod',
            'weekStart',
            'weekEnd',
            'monthStart',
            'monthEnd'
        ));
    }

    public function riderDashboard(Rider $rider)
    {
        // Riders can only see their own dashboard
        if (auth()->user()->isRider() && auth()->user()->rider?->id !== $rider->id) {
            abort(403);
        }

        $thisWeekStart = now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $thisWeekEnd   = now()->endOfWeek(\Carbon\Carbon::SUNDAY);

        $weekAttendance = $rider->attendances()
            ->whereBetween('date', [$thisWeekStart, $thisWeekEnd])
            ->orderBy('date')
            ->get();

        $pendingCa = $rider->cashAdvances()->where('is_deducted', false)->sum('amount');

        $latestPayslip = $rider->payslips()->latest('week_start')->first();

        return view('dashboard.rider', compact('rider', 'weekAttendance', 'pendingCa', 'latestPayslip'));
    }
}
