<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use App\Models\SpxAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SpxAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = SpxAccount::withCount([
            'attendances as today_riders' => fn($q) => $q->whereDate('date', today())
                                                          ->whereIn('status', ['present', 'half_day']),
            'riders as assigned_riders'   => fn($q) => $q->where('is_active', true),
        ]);

        if ($request->filled('search')) {
            $query->where('account_code', 'like', '%' . $request->search . '%')
                  ->orWhere('account_name', 'like', '%' . $request->search . '%');
        }

        $accounts = $query->orderBy('account_code')->paginate(15)->withQueryString();

        return view('spx-accounts.index', compact('accounts'));
    }

    public function show(SpxAccount $spxAccount)
    {
        $spxAccount->load(['riders' => fn($q) => $q->with('user')->orderBy('name')]);

        // Riders not yet assigned to this account (unassigned or assigned elsewhere)
        $availableRiders = Rider::where('is_active', true)
            ->where(fn($q) => $q->whereNull('spx_account_id')
                                ->orWhere('spx_account_id', '!=', $spxAccount->id))
            ->orderBy('name')
            ->get();

        // Today's attendance summary for this account
        $todayAttendance = $spxAccount->attendances()
            ->with('rider')
            ->whereDate('date', today())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('spx-accounts.show', compact('spxAccount', 'availableRiders', 'todayAttendance'));
    }

    public function create()
    {
        return view('spx-accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_code' => 'required|string|max:50|unique:spx_accounts',
            'account_name' => 'required|string|max:255',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        SpxAccount::create([
            'account_code' => $validated['account_code'],
            'account_name' => $validated['account_name'],
            'notes'        => $validated['notes'] ?? null,
            'is_active'    => $request->boolean('is_active', true),
        ]);

        return redirect()->route('spx-accounts.index')
            ->with('success', 'SPX Account created successfully.');
    }

    public function edit(SpxAccount $spxAccount)
    {
        return view('spx-accounts.edit', compact('spxAccount'));
    }

    public function update(Request $request, SpxAccount $spxAccount)
    {
        $validated = $request->validate([
            'account_code' => ['required', 'string', 'max:50', Rule::unique('spx_accounts')->ignore($spxAccount->id)],
            'account_name' => 'required|string|max:255',
            'notes'        => 'nullable|string',
            'is_active'    => 'boolean',
        ]);

        $spxAccount->update([
            'account_code' => $validated['account_code'],
            'account_name' => $validated['account_name'],
            'notes'        => $validated['notes'] ?? null,
            'is_active'    => $request->boolean('is_active'),
        ]);

        return redirect()->route('spx-accounts.show', $spxAccount)
            ->with('success', 'SPX Account updated successfully.');
    }

    public function destroy(SpxAccount $spxAccount)
    {
        // Unassign all riders before deleting
        Rider::where('spx_account_id', $spxAccount->id)
             ->update(['spx_account_id' => null]);

        $spxAccount->delete();

        return redirect()->route('spx-accounts.index')
            ->with('success', 'SPX Account deleted.');
    }

    // ─── Assignment actions ───────────────────────────────────────────────────

    /** Assign one or more riders to this SPX account */
    public function assignRiders(Request $request, SpxAccount $spxAccount)
    {
        $request->validate([
            'rider_ids'   => 'required|array|min:1',
            'rider_ids.*' => 'exists:riders,id',
        ]);

        Rider::whereIn('id', $request->rider_ids)
             ->update(['spx_account_id' => $spxAccount->id]);

        $count = count($request->rider_ids);

        return back()->with('success', "{$count} rider(s) assigned to {$spxAccount->account_code}.");
    }

    /** Remove a specific rider from this SPX account */
    public function unassignRider(SpxAccount $spxAccount, Rider $rider)
    {
        if ($rider->spx_account_id !== $spxAccount->id) {
            return back()->with('error', 'Rider is not assigned to this account.');
        }

        $rider->update(['spx_account_id' => null]);

        return back()->with('success', "{$rider->name} has been unassigned from {$spxAccount->account_code}.");
    }

    /** Assign a rider to an SPX account directly from the rider profile */
    public function assignFromRider(Request $request, Rider $rider)
    {
        $request->validate([
            'spx_account_id' => 'nullable|exists:spx_accounts,id',
        ]);

        $rider->update(['spx_account_id' => $request->spx_account_id ?: null]);

        $message = $request->spx_account_id
            ? 'Rider assigned to SPX account.'
            : 'Rider unassigned from SPX account.';

        return back()->with('success', $message);
    }
}
