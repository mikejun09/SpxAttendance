@extends('layouts.app')
@section('title', 'Weekly Cut-off — Bulk Payroll')
@section('page-title', 'Weekly Cut-off')
@section('breadcrumb', 'Payslips / Bulk Payroll Generation')

@push('styles')
<style>
    /* ── Week navigator ─────────────────────────────────── */
    .week-nav {
        display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
    }
    .week-badge {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 8px 16px; border-radius: 20px;
        background: rgba(249,115,22,.12); border: 1px solid rgba(249,115,22,.3);
        color: var(--accent); font-size: 13px; font-weight: 600;
    }

    /* ── Rider table ─────────────────────────────────────── */
    .rider-check { width: 18px; height: 18px; accent-color: var(--accent); cursor: pointer; }

    tr.row-existing td { opacity: .45; }
    tr.row-existing .badge-existing { opacity: 1; }

    .net-cell { font-weight: 700; font-size: 15px; }

    /* ── Confirm bar ─────────────────────────────────────── */
    .confirm-bar {
        position: sticky; bottom: 0; left: 0; right: 0;
        background: var(--bg-secondary);
        border-top: 1px solid var(--border);
        padding: 14px 28px;
        display: flex; align-items: center; gap: 14px; flex-wrap: wrap;
        z-index: 40;
        margin-left: calc(-28px); /* offset content padding */
        margin-right: calc(-28px);
        margin-bottom: -28px;
    }
    .confirm-bar .selected-count {
        font-size: 13px; color: var(--text-muted);
        flex: 1;
    }
    .confirm-bar .selected-count strong { color: var(--accent); }

    /* Progress overlay */
    #progress-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.65);
        z-index: 999;
        align-items: center; justify-content: center;
        flex-direction: column; gap: 16px;
    }
    #progress-overlay.show { display: flex; }
    .progress-spinner {
        width: 48px; height: 48px;
        border: 4px solid rgba(249,115,22,.3);
        border-top-color: var(--accent);
        border-radius: 50%;
        animation: spin .8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .progress-text { color: #fff; font-size: 15px; font-weight: 600; }
</style>
@endpush

@section('content')

{{-- Progress overlay --}}
<div id="progress-overlay">
    <div class="progress-spinner"></div>
    <div class="progress-text">Generating payslips… please wait</div>
</div>

<div class="page-header">
    <div>
        <h1>Weekly Cut-off</h1>
        <p>Generate payslips for all active riders in one batch for the selected week.</p>
    </div>
    <a href="{{ route('payslips.index') }}" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Back to Payslips
    </a>
</div>

{{-- ── Week selector ──────────────────────────────────────────────── --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-title" style="margin-bottom:14px;">
        <i class="fa-solid fa-calendar-week" style="color:var(--accent)"></i> Select Pay Period
    </div>
    <form method="GET" action="{{ route('payslips.bulk-cutoff') }}" class="week-nav">
        <div class="date-picker">
            <i class="fa-solid fa-calendar dp-icon"></i>
            <input type="date" name="week_start"
                   value="{{ $weekStart->toDateString() }}"
                   title="Any date in the desired week — system picks the Monday">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-rotate"></i> Refresh Preview
        </button>
        <div class="week-badge">
            <i class="fa-solid fa-calendar-check"></i>
            {{ $weekStart->format('M d') }} – {{ $weekEnd->format('M d, Y') }}
            &nbsp;·&nbsp; Week {{ $weekStart->weekOfYear }}
        </div>
    </form>
</div>

@php
    $eligibleRows = $preview->where('existing', false);
    $alreadyRows  = $preview->where('existing', true);
@endphp

{{-- ── Rider preview table + form ─────────────────────────────────── --}}
@if($preview->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fa-solid fa-users-slash"></i>
            <p>No active riders with billable attendance found for this week. <a href="{{ route('attendance.index') }}" style="color:var(--accent)">View Attendance</a></p>
        </div>
    </div>
@else
<form method="POST" action="{{ route('payslips.bulk-cutoff.store') }}" id="bulkForm">
    @csrf
    <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">

    <div class="card" style="padding:0; overflow:hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 20px; border-bottom:1px solid var(--border); flex-wrap:wrap; gap:10px;">
            <div class="card-title" style="margin:0;">
                <i class="fa-solid fa-users" style="color:var(--accent)"></i>
                Rider Payroll Preview — {{ $weekStart->format('M d') }}–{{ $weekEnd->format('M d, Y') }}
            </div>
            <div style="display:flex; align-items:center; gap:10px; font-size:13px;">
                <label style="display:flex; align-items:center; gap:6px; cursor:pointer; color:var(--text-secondary); margin:0;">
                    <input type="checkbox" id="checkAll" class="rider-check" checked>
                    Select / Deselect All Eligible
                </label>
            </div>
        </div>

        <div class="table-wrap" style="border:none; border-radius:0;">
            <table>
                <thead>
                    <tr>
                        <th style="width:36px;"></th>
                        <th>Rider</th>
                        <th>Days</th>
                        <th>Daily Rate</th>
                        <th>Gross Pay</th>
                        <th>CA Deduction</th>
                        <th>Prior Balance</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($preview->sortBy(fn($r) => $r['rider']->name) as $row)
                    @php $r = $row['rider']; $exists = $row['existing']; @endphp
                    <tr class="{{ $exists ? 'row-existing' : '' }}" id="row-{{ $r->id }}">
                        <td>
                            @if(!$exists)
                            <input type="checkbox"
                                   name="rider_ids[]"
                                   value="{{ $r->id }}"
                                   class="rider-check row-checkbox"
                                   checked>
                            @else
                            <i class="fa-solid fa-lock" style="color:var(--text-muted); font-size:13px;" title="Payslip already exists"></i>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:600;">{{ $r->name }}</div>
                            @if($r->employee_id)
                            <div style="font-size:11px; color:var(--text-muted);">{{ $r->employee_id }}</div>
                            @endif
                        </td>
                        <td>
                            <span>{{ $row['days_worked'] }}{{ $row['half_days'] ? ' + '.$row['half_days'].'×½' : '' }}</span>
                            <div style="font-size:11px; color:var(--text-muted);">= {{ $row['total_days'] }} days</div>
                        </td>
                        <td style="color:var(--text-muted);">₱{{ number_format($r->daily_rate, 2) }}</td>
                        <td>₱{{ number_format($row['gross_pay'], 2) }}</td>
                        <td>
                            @if($row['ca_total'] > 0)
                                <span style="color:var(--danger); font-weight:600;">– ₱{{ number_format($row['ca_total'], 2) }}</span>
                                <div style="font-size:11px; color:var(--text-muted);">{{ $row['pending_ca']->count() }} advance(s)</div>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($row['carried_balance'] > 0)
                                <span style="color:var(--danger); font-weight:600;">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    – ₱{{ number_format($row['carried_balance'], 2) }}
                                </span>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td class="net-cell" style="color:{{ $row['net_pay'] > 0 ? 'var(--success)' : 'var(--danger)' }}">
                            ₱{{ number_format($row['net_pay'], 2) }}
                        </td>
                        <td>
                            @if($exists)
                                <span class="badge badge-success badge-existing">
                                    <i class="fa-solid fa-check"></i> Done
                                </span>
                            @else
                                <span class="badge badge-warning" id="badge-{{ $r->id }}">
                                    <i class="fa-solid fa-clock"></i> Pending
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ── Confirm bar ──────────────────────────────────── --}}
    <div class="confirm-bar">
        <div class="selected-count">
            <strong id="selected-count">{{ $eligibleRows->count() }}</strong> rider(s) selected for payslip generation
        </div>
        <div style="display:flex; align-items:center; gap:10px;">
            <label style="font-size:13px; color:var(--text-secondary); margin:0;">Status:</label>
            <select name="status" style="width:auto; padding:8px 12px; font-size:13px;">
                <option value="final">Final</option>
                <option value="draft">Draft</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary" id="submitBtn"
                style="padding:11px 26px; font-size:14px;"
                onclick="document.getElementById('progress-overlay').classList.add('show')">
            <i class="fa-solid fa-bolt"></i>
            Generate Payslips (<span id="btn-count">{{ $eligibleRows->count() }}</span> riders)
        </button>
    </div>
</form>
@endif

@push('scripts')
<script>
(function () {
    const checkAll  = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.row-checkbox');
    const countEl   = document.getElementById('selected-count');
    const btnCount  = document.getElementById('btn-count');
    const submitBtn = document.getElementById('submitBtn');

    function updateCount() {
        const n = document.querySelectorAll('.row-checkbox:checked').length;
        countEl.textContent = n;
        btnCount.textContent = n;
        submitBtn.disabled = n === 0;
    }

    checkAll?.addEventListener('change', function () {
        rowChecks.forEach(cb => cb.checked = this.checked);
        updateCount();
    });

    rowChecks.forEach(cb => cb.addEventListener('change', function () {
        checkAll.checked = [...rowChecks].every(c => c.checked);
        updateCount();
    }));

    updateCount();

    // Prevent double-submit
    document.getElementById('bulkForm')?.addEventListener('submit', function () {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing…';
    });
})();
</script>
@endpush
@endsection
