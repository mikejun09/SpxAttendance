<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Rider;
use App\Models\WeeklyIncome;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::orderBy('date', 'desc')->paginate(15, ['*'], 'expenses_page');
        $incomes  = WeeklyIncome::orderBy('week_start', 'desc')->paginate(15, ['*'], 'incomes_page');

        // Fetch riders with carried_balance or pending cash advances
        $ridersQuery = Rider::with(['pendingCashAdvances']);
        
        if ($request->filled('rider_search')) {
            $search = $request->rider_search;
            $ridersQuery->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('employee_id', 'like', '%' . $search . '%');
            });
        }
        
        $filterBalance = $request->input('filter_balance', 'has_balance');
        if ($filterBalance === 'has_balance') {
            $ridersQuery->where(function($q) {
                $q->where('carried_balance', '>', 0)
                  ->orWhereHas('pendingCashAdvances');
            });
        }
        
        $outstandingRiders = $ridersQuery->orderBy('name')->paginate(15, ['*'], 'riders_page')->withQueryString();

        return view('financials.index', compact('expenses', 'incomes', 'outstandingRiders'));
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'description' => 'required|string|max:255',
        ]);

        Expense::create($validated);

        return redirect()->route('financials.index')
            ->with('success', 'Expense record added successfully.');
    }

    public function destroyExpense(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('financials.index')
            ->with('success', 'Expense record deleted successfully.');
    }

    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'amount'     => 'required|numeric|min:0.01',
            'week_start' => 'required|date',
            'notes'      => 'nullable|string|max:255',
        ]);

        // Snap input date to the starting Monday of the week
        $weekStart = Carbon::parse($validated['week_start'])->startOfWeek(Carbon::MONDAY)->toDateString();

        WeeklyIncome::create([
            'amount'     => $validated['amount'],
            'week_start' => $weekStart,
            'notes'      => $validated['notes'],
        ]);

        return redirect()->route('financials.index')
            ->with('success', 'Weekly income record added successfully.');
    }

    public function destroyIncome(WeeklyIncome $income)
    {
        $income->delete();

        return redirect()->route('financials.index')
            ->with('success', 'Weekly income record deleted successfully.');
    }

    public function clearBalance(Rider $rider)
    {
        $rider->update(['carried_balance' => 0]);

        return redirect()->route('financials.index', ['active_tab' => 'balances'])
            ->with('success', "Outstanding balance for {$rider->name} has been cleared.");
    }
}
