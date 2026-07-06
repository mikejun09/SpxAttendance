<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use App\Models\SpxAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RiderController extends Controller
{
    public function index(Request $request)
    {
        $query = Rider::with('user')->withCount(['attendances', 'cashAdvances']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_id', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $riders = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('riders.index', compact('riders'));
    }

    public function create()
    {
        return view('riders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'employee_id'    => 'nullable|string|max:50|unique:riders',
            'contact_number' => 'nullable|string|max:20',
            'daily_rate'     => 'required|numeric|min:0',
            'is_active'      => 'boolean',
            // optional: create a login account for rider
            'create_account' => 'boolean',
            'email'          => 'nullable|required_if:create_account,1|email|unique:users',
            'password'       => 'nullable|required_if:create_account,1|min:6',
        ]);

        $userId = null;
        if ($request->boolean('create_account') && $request->filled('email')) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'rider',
            ]);
            $userId = $user->id;
        }

        Rider::create([
            'name'           => $validated['name'],
            'employee_id'    => $validated['employee_id'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'daily_rate'     => $validated['daily_rate'],
            'is_active'      => $request->boolean('is_active', true),
            'user_id'        => $userId,
        ]);

        return redirect()->route('riders.index')
            ->with('success', 'Rider created successfully.');
    }

    public function show(Rider $rider)
    {
        $rider->load(['user', 'spxAccount', 'attendances.spxAccount', 'cashAdvances', 'payslips']);

        $recentAttendance = $rider->attendances()->with('spxAccount')
            ->orderBy('date', 'desc')->take(14)->get();

        $pendingCashAdvances = $rider->cashAdvances()
            ->where('is_deducted', false)->orderBy('date', 'desc')->get();

        $spxAccounts = SpxAccount::where('is_active', true)->orderBy('account_code')->get();

        return view('riders.show', compact('rider', 'recentAttendance', 'pendingCashAdvances', 'spxAccounts'));
    }

    public function edit(Rider $rider)
    {
        $rider->load('user');
        return view('riders.edit', compact('rider'));
    }

    public function update(Request $request, Rider $rider)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'employee_id'    => ['nullable', 'string', 'max:50', Rule::unique('riders')->ignore($rider->id)],
            'contact_number' => 'nullable|string|max:20',
            'daily_rate'     => 'required|numeric|min:0',
            'is_active'      => 'boolean',
        ]);

        $rider->update([
            'name'           => $validated['name'],
            'employee_id'    => $validated['employee_id'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'daily_rate'     => $validated['daily_rate'],
            'is_active'      => $request->boolean('is_active'),
        ]);

        return redirect()->route('riders.show', $rider)
            ->with('success', 'Rider updated successfully.');
    }

    public function destroy(Rider $rider)
    {
        $rider->delete();
        return redirect()->route('riders.index')
            ->with('success', 'Rider deleted successfully.');
    }
}
