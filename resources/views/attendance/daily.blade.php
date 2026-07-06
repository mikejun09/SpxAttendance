@extends('layouts.app')
@section('title', 'Mark Attendance — ' . \Carbon\Carbon::parse($date)->format('M d, Y'))
@section('page-title', 'Mark Attendance')
@section('breadcrumb', 'Attendance / ' . \Carbon\Carbon::parse($date)->format('l, F d, Y'))

@push('styles')
<style>
    .att-table th, .att-table td { padding: 10px 12px; }
    .att-table select, .att-table input[type="text"] { padding: 7px 10px; font-size:13px; }
    .status-radio { display:flex; gap:6px; flex-wrap:wrap; }
    .status-radio input[type="radio"] { display:none; }
    .status-radio label {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        border: 1px solid var(--border);
        color: var(--text-muted);
        transition: all .2s;
        white-space: nowrap;
    }
    .status-radio input[type="radio"]:checked + label.present  { background: rgba(34,197,94,.2);  color: var(--success); border-color: var(--success); }
    .status-radio input[type="radio"]:checked + label.absent   { background: rgba(239,68,68,.2);  color: var(--danger);  border-color: var(--danger); }
    .status-radio input[type="radio"]:checked + label.half_day { background: rgba(245,158,11,.2); color: var(--warning); border-color: var(--warning); }
    .status-radio input[type="radio"]:checked + label.rest_day { background: rgba(100,116,139,.2);color: var(--text-muted); border-color: var(--text-muted); }
    .status-radio label:hover { border-color: var(--accent); color: var(--text-primary); }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h1>Create Attendance</h1>
        <p>{{ $parsedDate->format('l, F d, Y') }}</p>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
        <a href="{{ route('attendance.daily', $parsedDate->copy()->subDay()->toDateString()) }}"
           class="btn btn-secondary btn-sm" title="Previous day">
            <i class="fa-solid fa-chevron-left"></i>
        </a>

        <div class="date-picker date-picker-lg">
            <i class="fa-solid fa-calendar-days dp-icon"></i>
            <input type="date" id="datePicker" value="{{ $date }}"
                   title="Pick a date"
                   onchange="window.location.href='{{ url('/attendance/daily') }}/' + this.value">
        </div>

        <a href="{{ route('attendance.daily', $parsedDate->copy()->addDay()->toDateString()) }}"
           class="btn btn-secondary btn-sm" title="Next day">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>
</div>

@if($riders->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fa-solid fa-user-slash"></i>
            <p>No active riders found. <a href="{{ route('riders.create') }}" style="color:var(--accent)">Add a rider first</a>.</p>
        </div>
    </div>
@else
<form method="POST" action="{{ route('attendance.bulk') }}" id="attendanceForm">
    @csrf
    <input type="hidden" name="date" value="{{ $date }}">

    <div class="card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:12px;">
            <div style="display:flex; gap:10px; align-items:center;">
                <div class="card-title"><i class="fa-solid fa-users" style="color:var(--accent)"></i> {{ $riders->count() }} Riders</div>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="markAll('present')">
                        <i class="fa-solid fa-check"></i> All Present
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="markAll('rest_day')">
                        <i class="fa-solid fa-moon"></i> All Rest Day
                    </button>
                </div>
            </div>
            <div class="form-group" style="margin:0; min-width:200px;">
                <label>Default SPX Account for all</label>
                <select id="bulkSpx">
                    <option value="">— Select to apply all —</option>
                    @foreach($spxAccounts as $acct)
                        <option value="{{ $acct->id }}">{{ $acct->account_code }} — {{ $acct->account_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="table-wrap">
            <table class="att-table">
                <thead>
                    <tr>
                        <th width="30%">Rider</th>
                        <th width="30%">Status</th>
                        <th width="25%">SPX Account</th>
                        <th>Notes</th> 
                    </tr>
                </thead>
                <tbody>
                    @foreach($riders as $rider)
                    @php $currentStatus = $existingAttendance[$rider->id] ?? 'present'; @endphp
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $rider->name }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">₱{{ number_format($rider->daily_rate, 2) }}/day</div>
                            @if($rider->spxAccount)
                                <div style="margin-top:4px;">
                                    <span style="font-size:10px; background:rgba(249,115,22,.1); color:var(--accent); padding:2px 7px; border-radius:10px; font-weight:600;">
                                        <i class="fa-solid fa-link" style="font-size:9px;"></i> {{ $rider->spxAccount->account_code }}
                                    </span>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="status-radio">
                                <input type="radio" name="attendance[{{ $rider->id }}][status]" id="p_{{ $rider->id }}" value="present" {{ $currentStatus === 'present' ? 'checked' : '' }}>
                                <label for="p_{{ $rider->id }}" class="present">Present</label>

                                <!-- <input type="radio" name="attendance[{{ $rider->id }}][status]" id="a_{{ $rider->id }}" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }}>
                                <label for="a_{{ $rider->id }}" class="absent">Absent</label>

                                <input type="radio" name="attendance[{{ $rider->id }}][status]" id="h_{{ $rider->id }}" value="half_day" {{ $currentStatus === 'half_day' ? 'checked' : '' }}>
                                <label for="h_{{ $rider->id }}" class="half_day">Half Day</label> -->

                                <input type="radio" name="attendance[{{ $rider->id }}][status]" id="r_{{ $rider->id }}" value="rest_day" {{ $currentStatus === 'rest_day' ? 'checked' : '' }}>
                                <label for="r_{{ $rider->id }}" class="rest_day">Rest Day</label>
                            </div>
                        </td>
                        <td>
                            <select name="attendance[{{ $rider->id }}][spx_id]" class="spx-select">
                                <option value="">— None —</option>
                                @foreach($spxAccounts as $acct)
                                    <option value="{{ $acct->id }}"
                                        {{ ($defaultSpx[$rider->id] ?? null) == $acct->id ? 'selected' : '' }}>
                                        {{ $acct->account_code }} — {{ $acct->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="attendance[{{ $rider->id }}][notes]"
                                   placeholder="Optional…"
                                   value="{{ $existingNotes[$rider->id] ?? '' }}">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:flex; gap:10px; margin-top:16px; position:sticky; bottom:20px;">
        <button type="submit" class="btn btn-primary" style="padding:12px 28px; font-size:15px; box-shadow:0 4px 20px rgba(249,115,22,.4);">
            <i class="fa-solid fa-floppy-disk"></i> Save Attendance
        </button>
        <a href="{{ route('attendance.index', ['date' => $date]) }}" class="btn btn-secondary">View Records</a>
    </div>
</form>
@endif

@push('scripts')
<script>
    // Mark all riders with a single status
    function markAll(status) {
        document.querySelectorAll(`input[type="radio"][value="${status}"]`).forEach(r => r.checked = true);
    }

    // Apply bulk SPX account to all selects
    document.getElementById('bulkSpx').addEventListener('change', function() {
        if (!this.value) return;
        document.querySelectorAll('.spx-select').forEach(s => s.value = this.value);
    });
</script>
@endpush
@endsection
