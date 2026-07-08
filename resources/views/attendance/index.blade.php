@extends('layouts.app')
@section('title', 'Attendance')
@section('page-title', 'Attendance')
@section('breadcrumb', 'View & manage attendance records')

@section('content')
<div class="page-header">
    <div><h1>Attendance Records</h1><p>Filtered by date</p></div>
    <a href="{{ route('attendance.daily', today()->toDateString()) }}" class="btn btn-primary">
        <i class="fa-solid fa-calendar-day"></i> Create Attendance
    </a>
</div>

<div class="card">
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label>Date</label>
            <div class="date-picker">
                <i class="fa-solid fa-calendar-days dp-icon"></i>
                <input type="date" name="date" id="attendanceDateFilter"
                       value="{{ $date }}"
                       onchange="this.closest('form').submit()"
                       title="Click to pick a date">
            </div>
        </div>
        <div class="form-group">
            <label>SPX Account</label>
            <select name="spx_account_id">
                <option value="">All Accounts</option>
                @foreach($spxAccounts as $acct)
                    <option value="{{ $acct->id }}" {{ request('spx_account_id') == $acct->id ? 'selected' : '' }}>
                        {{ $acct->account_code }} — {{ $acct->account_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">All Status</option>
                <option value="present"  {{ request('status') === 'present'  ? 'selected' : '' }}>Present</option>
                <option value="rest_day" {{ request('status') === 'rest_day' ? 'selected' : '' }}>Rest Day</option>
            </select>
        </div>
        <div class="form-group" style="display:flex; gap:8px; align-items:flex-end;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
            <a href="{{ route('attendance.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    @if($attendances->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-calendar-xmark"></i>
            <p>No attendance records for this filter.</p>
        </div>
    @else
        <div style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">
            Showing attendance for <strong>{{ \Carbon\Carbon::parse($date)->format('l, F d, Y') }}</strong>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rider</th>
                        <th>SPX Account</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Recorded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $att)
                    <tr>
                        <td>
                            <a href="{{ route('riders.show', $att->rider) }}" style="color:var(--text-primary); text-decoration:none; font-weight:500;">
                                {{ $att->rider->name }}
                            </a>
                        </td>
                        <td>
                            @if($att->spxAccount)
                                <span class="badge badge-accent">{{ $att->spxAccount->account_name }}</span>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @php $bc = match($att->status) { 'present'=>'badge-success','absent'=>'badge-danger','half_day'=>'badge-warning',default=>'badge-muted' }; @endphp
                            <span class="badge {{ $bc }}">{{ $att->status_label }}</span>
                        </td>
                        <td style="font-size:12px; color:var(--text-muted);">{{ $att->notes ?? '—' }}</td>
                        <td style="font-size:12px; color:var(--text-muted);">{{ $att->created_at->format('h:i A') }}</td>
                        <td>
                            <form method="POST" action="{{ route('attendance.destroy', $att) }}" onsubmit="return confirm('Delete this record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-icon">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $attendances->links() }}</div>
    @endif
</div>

{{-- Date navigation --}}
<div style="display:flex; gap:10px; margin-top:16px; justify-content:center; align-items:center;">
    <a href="{{ route('attendance.index', ['date' => \Carbon\Carbon::parse($date)->subDay()->toDateString()]) }}"
       class="btn btn-secondary btn-sm" title="Previous day">
        <i class="fa-solid fa-chevron-left"></i> Prev
    </a>

    <div class="date-picker date-picker-lg">
        <i class="fa-solid fa-calendar-days dp-icon"></i>
        <input type="date" id="attendanceDateNav" value="{{ $date }}"
               title="Jump to date"
               onchange="window.location.href='{{ route('attendance.index') }}?date=' + this.value">
    </div>

    <a href="{{ route('attendance.daily', $date) }}" class="btn btn-primary btn-sm">
        <i class="fa-solid fa-pen"></i> Edit This Day
    </a>

    <a href="{{ route('attendance.index', ['date' => \Carbon\Carbon::parse($date)->addDay()->toDateString()]) }}"
       class="btn btn-secondary btn-sm" title="Next day">
        Next <i class="fa-solid fa-chevron-right"></i>
    </a>
</div>
@endsection
