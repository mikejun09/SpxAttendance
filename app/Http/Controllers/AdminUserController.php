<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminUserController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->orderBy('name')
            ->paginate(15);

        return view('admin-users.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin user.
     */
    public function create()
    {
        return view('admin-users.create');
    }

    /**
     * Store a newly created admin user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'admin',
        ]);

        return redirect()->route('admin-users.index')
            ->with('success', 'Admin user created successfully.');
    }

    /**
     * Show the form for editing the specified admin user.
     */
    public function edit(User $adminUser)
    {
        if ($adminUser->role !== 'admin') {
            abort(403);
        }

        return view('admin-users.edit', compact('adminUser'));
    }

    /**
     * Update the specified admin user.
     */
    public function update(Request $request, User $adminUser)
    {
        if ($adminUser->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $adminUser->id],
        ]);

        $adminUser->update($validated);

        return redirect()->route('admin-users.index')
            ->with('success', 'Admin user updated successfully.');
    }

    /**
     * Reset the specified admin user's password to "password".
     */
    public function resetPassword(User $adminUser)
    {
        if ($adminUser->role !== 'admin') {
            abort(403);
        }

        $adminUser->update([
            'password' => Hash::make('password'),
        ]);

        return redirect()->route('admin-users.index')
            ->with('success', "Password for {$adminUser->name} has been reset to \"password\".");
    }
}
