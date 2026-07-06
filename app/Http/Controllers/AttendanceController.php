<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Rider;
use App\Models\SpxAccount;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date') ? $request->date : today()->toDateString();

        $query = Attendance::with(['rider', 'spxAccount'])
            ->whereDate('date', $date);

        if ($request->filled('spx_account_id')) {
            $query->where('spx_account_id', $request->spx_account_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $spxAccounts = SpxAccount::where('is_active', true)->orderBy('account_code')->get();

        return view('attendance.index', compact('attendances', 'date', 'spxAccounts'));
    }

    public function daily(Request $request, string $date = null)
    {
        $date = $date ?? today()->toDateString();
        $parsedDate = Carbon::parse($date);

        $riders = Rider::where('is_active', true)
                        ->with('spxAccount')
                        ->orderBy('name')
                        ->get();

        $spxAccounts = SpxAccount::where('is_active', true)->orderBy('account_code')->get();

        // Load existing attendance records for this date
        $existingAttendance = Attendance::whereDate('date', $date)
            ->pluck('status', 'rider_id')
            ->toArray();

        $existingSpx = Attendance::whereDate('date', $date)
            ->pluck('spx_account_id', 'rider_id')
            ->toArray();

        $existingNotes = Attendance::whereDate('date', $date)
            ->pluck('notes', 'rider_id')
            ->toArray();

        // Build default SPX map: use existing attendance record first,
        // then fall back to the rider's standing assignment
        $defaultSpx = [];
        foreach ($riders as $rider) {
            $defaultSpx[$rider->id] = $existingSpx[$rider->id]
                ?? $rider->spx_account_id
                ?? null;
        }

        return view('attendance.daily', compact(
            'date', 'parsedDate', 'riders', 'spxAccounts',
            'existingAttendance', 'existingSpx', 'existingNotes', 'defaultSpx'
        ));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'date'                   => 'required|date',
            'attendance'             => 'required|array',
            'attendance.*.status'    => 'required|in:present,absent,rest_day,half_day',
            'attendance.*.spx_id'    => 'nullable|exists:spx_accounts,id',
            'attendance.*.notes'     => 'nullable|string|max:255',
        ]);

        $date = $request->date;

        foreach ($request->attendance as $riderId => $data) {
            Attendance::updateOrCreate(
                ['rider_id' => $riderId, 'date' => $date],
                [
                    'status'         => $data['status'],
                    'spx_account_id' => $data['spx_id'] ?? null,
                    'notes'          => $data['notes'] ?? null,
                ]
            );
        }

        return redirect()->route('attendance.daily', $date)
            ->with('success', 'Attendance saved for ' . Carbon::parse($date)->format('M d, Y') . '.');
    }

    public function destroy(Attendance $attendance)
    {
        $date = $attendance->date->toDateString();
        $attendance->delete();

        return back()->with('success', 'Attendance record deleted.');
    }
}
