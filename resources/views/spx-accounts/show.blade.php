@extends('layouts.app')
@section('title', $spxAccount->account_code . ' — ' . $spxAccount->account_name)
@section('page-title', $spxAccount->account_code)
@section('breadcrumb', 'SPX Accounts / ' . $spxAccount->account_name)

@push('styles')
<style>
    .rider-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        transition: background .15s;
    }
    .rider-row:last-child { border-bottom: none; }
    .rider-row:hover { background: var(--bg-hover); }

    .rider-avatar {
        width: 36px; height: 36px;
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; font-weight: 700;
        flex-shrink: 0;
    }

    .available-rider {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--bg-primary);
        margin-bottom: 8px;
        cursor: pointer;
        transition: all .2s;
    }
    .available-rider:hover { border-color: var(--accent); background: rgba(249,115,22,.05); }
    .available-rider input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--accent); flex-shrink:0; }
    .available-rider label { flex: 1; cursor: pointer; margin: 0; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="page-header">
    <div>
        <h1>
            <span style="color:var(--accent);">{{ $spxAccount->account_code }}</span>
            — {{ $spxAccount->account_name }}
        </h1>
        <p>
            <span class="status-dot {{ $spxAccount->is_active ? 'dot-green' : 'dot-red' }}"></span>
            {{ $spxAccount->is_active ? 'Active' : 'Inactive' }}
            &bull; {{ $spxAccount->riders->count() }} assigned rider(s)
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="{{ route('spx-accounts.edit', $spxAccount) }}" class="btn btn-secondary">
            <i class="fa-solid fa-pen"></i> Edit
        </a>
        <a href="{{ route('spx-accounts.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1fr 380px; gap:20px; align-items:start;">

    {{-- ── Left: Assigned Riders ── --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fa-solid fa-user-group" style="color:var(--accent)"></i>
                Assigned Riders
                <span style="margin-left:8px; background:var(--bg-hover); border:1px solid var(--border); border-radius:20px; padding:2px 10px; font-size:12px; font-weight:600;">
                    {{ $spxAccount->riders->count() }}
                </span>
            </div>
        </div>

        @if($spxAccount->riders->isEmpty())
            <div class="empty-state" style="padding:40px 20px;">
                <i class="fa-solid fa-user-plus"></i>
                <p>No riders assigned yet.<br>Use the panel on the right to assign riders.</p>
            </div>
        @else
            <div style="border-radius:8px; border:1px solid var(--border); overflow:hidden;">
                @foreach($spxAccount->riders as $rider)
                <div class="rider-row">
                    <div style="display:flex; align-items:center; gap:12px;">
                        <div class="rider-avatar">{{ strtoupper(substr($rider->name, 0, 1)) }}</div>
                        <div>
                            <div style="font-weight:600;">
                                <a href="{{ route('riders.show', $rider) }}" style="color:var(--text-primary); text-decoration:none;">
                                    {{ $rider->name }}
                                </a>
                            </div>
                            <div style="font-size:12px; color:var(--text-muted);">
                                {{ $rider->employee_id ?? 'No ID' }}
                                &bull; ₱{{ number_format($rider->daily_rate, 2) }}/day
                                @if($rider->user_id)
                                    &bull; <span style="color:var(--success);"><i class="fa-solid fa-circle-check"></i> Portal</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span class="badge {{ $rider->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $rider->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <form method="POST"
                              action="{{ route('spx-accounts.unassign', [$spxAccount, $rider]) }}"
                              onsubmit="return confirm('Remove {{ $rider->name }} from this account?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Unassign">
                                <i class="fa-solid fa-user-minus"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ── Right: Add Riders + Today's Attendance ── --}}
    <div>

        {{-- Assign form --}}
        <div class="card" style="margin-bottom:16px;">
            <div class="card-title" style="margin-bottom:14px;">
                <i class="fa-solid fa-user-plus" style="color:var(--accent)"></i> Assign Riders
            </div>

            @if($availableRiders->isEmpty())
                <div style="color:var(--text-muted); font-size:13px; text-align:center; padding:16px 0;">
                    <i class="fa-solid fa-check-circle" style="color:var(--success); margin-right:6px;"></i>
                    All active riders are already assigned here.
                </div>
            @else
                <form method="POST" action="{{ route('spx-accounts.assign', $spxAccount) }}" id="assignForm">
                    @csrf
                    <div style="max-height:320px; overflow-y:auto; margin-bottom:12px;">
                        @foreach($availableRiders as $rider)
                        <div class="available-rider">
                            <input type="checkbox" name="rider_ids[]" id="ar_{{ $rider->id }}" value="{{ $rider->id }}">
                            <label for="ar_{{ $rider->id }}">
                                <div style="font-weight:600; font-size:13px;">{{ $rider->name }}</div>
                                <div style="font-size:11px; color:var(--text-muted);">
                                    {{ $rider->employee_id ?? 'No ID' }} &bull; ₱{{ number_format($rider->daily_rate,2) }}/day
                                    @if($rider->spx_account_id)
                                        &bull; <span style="color:var(--warning);">Currently: {{ $rider->spxAccount?->account_code }}</span>
                                    @endif
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <div style="display:flex; gap:8px;">
                        <button type="submit" class="btn btn-primary" style="flex:1;">
                            <i class="fa-solid fa-user-plus"></i> Assign Selected
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAll()">
                            Select All
                        </button>
                    </div>
                </form>
            @endif
        </div>

        {{-- Today's attendance --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title" style="font-size:14px;">
                    <i class="fa-solid fa-clock" style="color:var(--accent)"></i> Today's Attendance
                </div>
                <span style="font-size:12px; color:var(--text-muted);">{{ now()->format('M d') }}</span>
            </div>

            @if($todayAttendance->isEmpty())
                <div style="color:var(--text-muted); font-size:13px; text-align:center; padding:12px 0;">
                    No attendance logged today for this account.
                </div>
            @else
                @foreach($todayAttendance as $att)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border); font-size:13px;">
                    <span>{{ $att->rider->name }}</span>
                    @php $bc = match($att->status) { 'present'=>'badge-success','absent'=>'badge-danger','half_day'=>'badge-warning',default=>'badge-muted' }; @endphp
                    <span class="badge {{ $bc }}">{{ $att->status_label }}</span>
                </div>
                @endforeach
            @endif

            <div style="margin-top:14px;">
                <a href="{{ route('attendance.daily', today()->toDateString()) }}" class="btn btn-primary btn-sm" style="width:100%; justify-content:center;">
                    <i class="fa-solid fa-calendar-check"></i> Go to Attendance Entry
                </a>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    let allChecked = false;
    function toggleAll() {
        allChecked = !allChecked;
        document.querySelectorAll('#assignForm input[type="checkbox"]').forEach(cb => cb.checked = allChecked);
    }
</script>
@endpush

@endsection
