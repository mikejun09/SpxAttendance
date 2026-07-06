<?php

namespace App\Http\Controllers;

use App\Models\CashAdvance;
use App\Models\Rider;
use Illuminate\Http\Request;

class CashAdvanceController extends Controller
{
    public function index(Request $request)
    {
        $query = CashAdvance::with('rider');

        if ($request->filled('rider_id')) {
            $query->where('rider_id', $request->rider_id);
        }

        if ($request->filled('status')) {
            $query->where('is_deducted', $request->status === 'deducted');
        }

        $cashAdvances = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();
        $riders = Rider::where('is_active', true)->orderBy('name')->get();

        return view('cash-advances.index', compact('cashAdvances', 'riders'));
    }

    public function create(Request $request)
    {
        $riders = Rider::where('is_active', true)->orderBy('name')->get();
        $selectedRider = $request->filled('rider_id') ? Rider::find($request->rider_id) : null;

        return view('cash-advances.create', compact('riders', 'selectedRider'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'rider_id' => 'required|exists:riders,id',
            'amount'   => 'required|numeric|min:1',
            'date'     => 'required|date',
            'notes'    => 'nullable|string|max:255',
        ]);

        CashAdvance::create([
            'rider_id'    => $validated['rider_id'],
            'amount'      => $validated['amount'],
            'date'        => $validated['date'],
            'notes'       => $validated['notes'] ?? null,
            'is_deducted' => false,
        ]);

        return redirect()->route('cash-advances.index')
            ->with('success', 'Cash advance recorded successfully.');
    }

    public function destroy(CashAdvance $cashAdvance)
    {
        // Get any associated payslips
        $payslips = $cashAdvance->payslips()->with('cashAdvances')->get();

        // Delete the cash advance
        $cashAdvance->delete();

        // Recalculate and update associated payslips
        foreach ($payslips as $payslip) {
            // Filter out the deleted cash advance from the pre-loaded relation collection
            $remainingCa = $payslip->cashAdvances->reject(fn($ca) => $ca->id === $cashAdvance->id);
            $newCaDeduction = $remainingCa->sum('amount');
            
            $payslip->cash_advance_deduction = $newCaDeduction;
            $payslip->net_pay = max(0, $payslip->gross_pay - $newCaDeduction - $payslip->manual_deduction);
            $payslip->save();
        }

        return back()->with('success', 'Cash advance deleted. Associated payslip(s) updated.');
    }
}
