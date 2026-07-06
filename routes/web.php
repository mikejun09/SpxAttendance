<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CashAdvanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RiderController;
use App\Http\Controllers\SpxAccountController;
use Illuminate\Support\Facades\Route;

// ── Public ─────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('dashboard'));

require __DIR__ . '/auth.php';

// ── Authenticated routes ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/rider/{rider}', [DashboardController::class, 'riderDashboard'])->name('rider.dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ─────────────────────────────────────────────────────────────────────────
    // PAYSLIPS — order matters: static routes MUST come before {wildcard} routes
    // ─────────────────────────────────────────────────────────────────────────

    // Admin-only payslip actions (no wildcard — must be first)
    Route::middleware('admin')->group(function () {
        Route::get('/payslips/create', [PayslipController::class, 'create'])->name('payslips.create');
        Route::post('/payslips', [PayslipController::class, 'store'])->name('payslips.store');

        // Bulk weekly cut-off
        Route::get('/payslips/bulk-cutoff', [PayslipController::class, 'bulkCutoffForm'])->name('payslips.bulk-cutoff');
        Route::post('/payslips/bulk-cutoff', [PayslipController::class, 'bulkCutoffStore'])->name('payslips.bulk-cutoff.store');

        // Payroll summary report (all riders, one page)
        Route::get('/payslips/report', [PayslipController::class, 'report'])->name('payslips.report');
    });

    // Read routes — accessible to both admin & riders
    Route::get('/payslips', [PayslipController::class, 'index'])->name('payslips.index');

    // Wildcard routes — must come AFTER all static /payslips/* routes
    Route::get('/payslips/{payslip}', [PayslipController::class, 'show'])->name('payslips.show');
    Route::get('/payslips/{payslip}/print', [PayslipController::class, 'print'])->name('payslips.print');
    Route::middleware('admin')->delete('/payslips/{payslip}', [PayslipController::class, 'destroy'])->name('payslips.destroy');

    // ── Admin-only routes ─────────────────────────────────────────────────
    Route::middleware('admin')->group(function () {

        // Admins
        Route::get('/admin-users', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('admin-users.index');
        Route::get('/admin-users/create', [\App\Http\Controllers\AdminUserController::class, 'create'])->name('admin-users.create');
        Route::post('/admin-users', [\App\Http\Controllers\AdminUserController::class, 'store'])->name('admin-users.store');
        Route::get('/admin-users/{adminUser}/edit', [\App\Http\Controllers\AdminUserController::class, 'edit'])->name('admin-users.edit');
        Route::put('/admin-users/{adminUser}', [\App\Http\Controllers\AdminUserController::class, 'update'])->name('admin-users.update');
        Route::post('/admin-users/{adminUser}/reset-password', [\App\Http\Controllers\AdminUserController::class, 'resetPassword'])->name('admin-users.reset-password');

        // Riders
        Route::resource('riders', RiderController::class);

        // SPX Accounts
        Route::resource('spx-accounts', SpxAccountController::class);
        Route::post('/spx-accounts/{spxAccount}/assign', [SpxAccountController::class, 'assignRiders'])->name('spx-accounts.assign');
        Route::delete('/spx-accounts/{spxAccount}/unassign/{rider}', [SpxAccountController::class, 'unassignRider'])->name('spx-accounts.unassign');
        Route::post('/riders/{rider}/assign-spx', [SpxAccountController::class, 'assignFromRider'])->name('riders.assign-spx');

        // Attendance
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/daily/{date?}', [AttendanceController::class, 'daily'])->name('attendance.daily');
        Route::post('/attendance/bulk', [AttendanceController::class, 'bulkStore'])->name('attendance.bulk');
        Route::delete('/attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

        // Cash Advances
        Route::get('/cash-advances', [CashAdvanceController::class, 'index'])->name('cash-advances.index');
        Route::get('/cash-advances/create', [CashAdvanceController::class, 'create'])->name('cash-advances.create');
        Route::post('/cash-advances', [CashAdvanceController::class, 'store'])->name('cash-advances.store');
        Route::delete('/cash-advances/{cashAdvance}', [CashAdvanceController::class, 'destroy'])->name('cash-advances.destroy');

        // Financial Records
        Route::get('/finance', [FinancialController::class, 'index'])->name('financials.index');
        Route::post('/finance/expenses', [FinancialController::class, 'storeExpense'])->name('financials.store_expense');
        Route::delete('/finance/expenses/{expense}', [FinancialController::class, 'destroyExpense'])->name('financials.destroy_expense');
        Route::post('/finance/income', [FinancialController::class, 'storeIncome'])->name('financials.store_income');
        Route::delete('/finance/income/{income}', [FinancialController::class, 'destroyIncome'])->name('financials.destroy_income');
    });
});
