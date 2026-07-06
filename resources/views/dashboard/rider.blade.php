@extends('layouts.app')

@section('title', $rider->name . ' — My Dashboard')
@section('page-title', $rider->name)
@section('breadcrumb', 'Rider Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon stat-icon-green"><i class="fa-solid fa-calendar-check"></i></div>
        <div>
            <div class="stat-value">{{ $weekAttendance->whereIn('status', ['present','half_day'])->count() }}</div>
            <div class="stat-label">Days This Week</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-orange"><i class="fa-solid fa-peso-sign"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($rider->daily_rate, 0) }}</div>
            <div class="stat-label">Daily Rate</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon stat-icon-yellow"><i class="fa-solid fa-money-bill-wave"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($pendingCa, 0) }}</div>
            <div class="stat-label">Pending Cash Advance</div>
        </div>
    </div>
    @if($latestPayslip)
    <div class="stat-card">
        <div class="stat-icon stat-icon-blue"><i class="fa-solid fa-file-invoice-dollar"></i></div>
        <div>
            <div class="stat-value">₱{{ number_format($latestPayslip->net_pay, 0) }}</div>
            <div class="stat-label">Latest Payslip Net Pay</div>
        </div>
    </div>
    @endif
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="fa-solid fa-calendar-week" style="color:var(--accent)"></i> This Week's Attendance</div>
        <span style="font-size:13px; color:var(--text-muted);">
            {{ now()->startOfWeek(\Carbon\Carbon::MONDAY)->format('M d') }} – {{ now()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('M d, Y') }}
        </span>
    </div>

    @if($weekAttendance->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-calendar-day"></i>
            <p>No attendance recorded this week yet.</p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Day</th>
                        <th>SPX Account</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($weekAttendance as $att)
                    <tr>
                        <td>{{ $att->date->format('M d, Y') }}</td>
                        <td style="color:var(--text-muted);">{{ $att->date->format('l') }}</td>
                        <td>
                            @if($att->spxAccount)
                                <span class="badge badge-accent">{{ $att->spxAccount->account_code }}</span>
                            @else
                                <span style="color:var(--text-muted);">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $bc = match($att->status) { 'present' => 'badge-success', 'absent' => 'badge-danger', 'half_day' => 'badge-warning', default => 'badge-muted' };
                            @endphp
                            <span class="badge {{ $bc }}">{{ $att->status_label }}</span>
                        </td>
                        <td style="color:var(--text-muted); font-size:12px;">{{ $att->notes ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@if($latestPayslip)
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <div class="card-title"><i class="fa-solid fa-file-invoice-dollar" style="color:var(--accent)"></i> Latest Payslip</div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('payslips.show', $latestPayslip) }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-eye"></i> View
            </a>
            <a href="{{ route('payslips.print', $latestPayslip) }}" target="_blank" class="btn btn-info btn-sm">
                <i class="fa-solid fa-print"></i> Print
            </a>
        </div>
    </div>
    <div class="form-row form-row-3" style="gap:12px;">
        <div style="background:var(--bg-hover); border-radius:8px; padding:16px; text-align:center;">
            <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">Period</div>
            <div style="font-weight:600; font-size:13px;">{{ $latestPayslip->period_label }}</div>
        </div>
        <div style="background:var(--bg-hover); border-radius:8px; padding:16px; text-align:center;">
            <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">Gross Pay</div>
            <div style="font-weight:700; font-size:18px; color:var(--accent);">₱{{ number_format($latestPayslip->gross_pay, 2) }}</div>
        </div>
        <div style="background:var(--bg-hover); border-radius:8px; padding:16px; text-align:center;">
            <div style="font-size:12px; color:var(--text-muted); margin-bottom:4px;">Net Pay</div>
            <div style="font-weight:700; font-size:18px; color:var(--success);">₱{{ number_format($latestPayslip->net_pay, 2) }}</div>
        </div>
    </div>
</div>
@endif
@endsection
