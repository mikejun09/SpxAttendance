@extends('layouts.app')
@section('title', $rider->name)
@section('page-title', $rider->name)
@section('breadcrumb', 'Riders / Profile')

@section('content')
{{-- Profile Header --}}
<div class="card" style="margin-bottom:20px;">
    <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
        <div style="width:72px; height:72px; background:linear-gradient(135deg,var(--accent),var(--accent-dark)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:800; flex-shrink:0;">
            {{ strtoupper(substr($rider->name, 0, 1)) }}
        </div>
        <div style="flex:1;">
            <div style="font-size:22px; font-weight:700;">{{ $rider->name }}</div>
            <div style="color:var(--text-muted); font-size:13px; margin-top:4px;">
                @if($rider->employee_id)<span style="font-family:monospace;">{{ $rider->employee_id }}</span> &bull; @endif
                @if($rider->contact_number){{ $rider->contact_number }} &bull; @endif
                <span class="status-dot {{ $rider->is_active ? 'dot-green' : 'dot-red' }}"></span>
                {{ $rider->is_active ? 'Active' : 'Inactive' }}
            </div>
        </div>
        <div style="display:flex; gap:8px;">
            <a href="{{ route('riders.edit', $rider) }}" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-pen"></i> Edit
            </a>
            <a href="{{ route('attendance.daily', today()->toDateString()) }}?rider={{ $rider->id }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-calendar-check"></i> Mark Attendance
            </a>
        </div>
    </div>

    <div class="divider"></div>

    <div style="display:grid; grid-template-columns: repeat(auto-fit,minmax(160px,1fr)); gap:16px;">
        <div style="text-align:center; padding:12px; background:var(--bg-hover); border-radius:8px;">
            <div style="font-size:22px; font-weight:800; color:var(--accent);">&#8369;{{ number_format($rider->daily_rate, 2) }}</div>
            <div style="font-size:12px; color:var(--text-muted);">Daily Rate</div>
        </div>
        <div style="text-align:center; padding:12px; background:var(--bg-hover); border-radius:8px;">
            <div style="font-size:22px; font-weight:800; color:var(--success);">{{ $rider->attendances->where('status','present')->count() }}</div>
            <div style="font-size:12px; color:var(--text-muted);">Total Present Days</div>
        </div>
        <div style="text-align:center; padding:12px; background:var(--bg-hover); border-radius:8px;">
            <div style="font-size:22px; font-weight:800; color:var(--warning);">&#8369;{{ number_format($pendingCashAdvances->sum('amount'), 2) }}</div>
            <div style="font-size:12px; color:var(--text-muted);">Pending Cash Advance</div>
        </div>
        <div style="text-align:center; padding:12px; background:var(--bg-hover); border-radius:8px;">
            <div style="font-size:22px; font-weight:800;">{{ $rider->payslips->count() }}</div>
            <div style="font-size:12px; color:var(--text-muted);">Total Payslips</div>
        </div>
    </div>

    {{-- SPX Account Assignment --}}
    <div class="divider"></div>
    <div style="display:flex; align-items:center; gap:16px; flex-wrap:wrap;">
        <div style="font-size:13px; font-weight:600; color:var(--text-secondary); white-space:nowrap;">
            <i class="fa-solid fa-boxes-stacked" style="color:var(--accent); margin-right:6px;"></i>
            SPX Account Assignment
        </div>
        @if($rider->spxAccount)
            <a href="{{ route('spx-accounts.show', $rider->spxAccount) }}"
               style="display:inline-flex; align-items:center; gap:6px; background:rgba(249,115,22,.15); color:var(--accent); border:1px solid rgba(249,115,22,.3); border-radius:20px; padding:4px 14px; font-size:13px; font-weight:700; text-decoration:none;">
                <i class="fa-solid fa-link" style="font-size:11px;"></i>
                {{ $rider->spxAccount->account_code }} &mdash; {{ $rider->spxAccount->account_name }}
            </a>
        @else
            <span style="color:var(--text-muted); font-size:13px;">No SPX account assigned</span>
        @endif

        {{-- Quick change form --}}
        <form method="POST" action="{{ route('riders.assign-spx', $rider) }}"
              style="display:flex; align-items:center; gap:8px; margin-left:auto;">
            @csrf
            <select name="spx_account_id" style="width:auto; min-width:200px; padding:7px 12px; font-size:13px;">
                <option value="">— Unassign —</option>
                @foreach($spxAccounts as $acct)
                    <option value="{{ $acct->id }}" {{ $rider->spx_account_id == $acct->id ? 'selected' : '' }}>
                        {{ $acct->account_code }} — {{ $acct->account_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-floppy-disk"></i> Save
            </button>
        </form>
    </div>
</div>

<div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; margin-top:20px;">


    {{-- Recent Attendance --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-calendar-check" style="color:var(--accent)"></i> Recent Attendance</div>
            <a href="{{ route('attendance.daily', today()->toDateString()) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i>
            </a>
        </div>

        @if($recentAttendance->isEmpty())
            <div class="empty-state" style="padding:20px"><p>No attendance records yet.</p></div>
        @else
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Date</th><th>SPX</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        @foreach($recentAttendance as $att)
                        <tr>
                            <td style="font-size:13px;">{{ $att->date->format('M d, Y') }}</td>
                            <td style="font-size:12px; color:var(--text-muted);">{{ $att->spxAccount?->account_code ?? '—' }}</td>
                            <td>
                                @php $bc = match($att->status) { 'present'=>'badge-success','absent'=>'badge-danger','half_day'=>'badge-warning',default=>'badge-muted' }; @endphp
                                <span class="badge {{ $bc }}">{{ $att->status_label }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Pending Cash Advances --}}
    <div class="card">
        <div class="card-header">
            <div class="card-title"><i class="fa-solid fa-money-bill-wave" style="color:var(--accent)"></i> Pending Cash Advances</div>
            <a href="{{ route('cash-advances.create', ['rider_id' => $rider->id]) }}" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i>
            </a>
        </div>

        @if($pendingCashAdvances->isEmpty())
            <div class="empty-state" style="padding:20px"><p>No pending cash advances.</p></div>
        @else
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Date</th><th>Amount</th><th>Notes</th><th></th></tr></thead>
                    <tbody>
                        @foreach($pendingCashAdvances as $ca)
                        <tr>
                            <td style="font-size:13px;">{{ $ca->date->format('M d, Y') }}</td>
                            <td><strong style="color:var(--warning);">₱{{ number_format($ca->amount, 2) }}</strong></td>
                            <td style="font-size:12px; color:var(--text-muted);">{{ $ca->notes ?? '—' }}</td>
                            <td>
                                <form method="POST" action="{{ route('cash-advances.destroy', $ca) }}" onsubmit="return confirm('Are you sure you want to delete this cash advance of ₱{{ number_format($ca->amount, 2) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

{{-- Payslips --}}
<div class="card" style="margin-top:20px;">
    <div class="card-header">
        <div class="card-title"><i class="fa-solid fa-file-invoice-dollar" style="color:var(--accent)"></i> Payslips</div>
        <a href="{{ route('payslips.create', ['rider_id' => $rider->id]) }}" class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus"></i> Generate
        </a>
    </div>

    @if($rider->payslips->isEmpty())
        <div class="empty-state" style="padding:20px"><p>No payslips generated yet.</p></div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Period</th><th>Days</th><th>Gross Pay</th><th>CA Deduction</th><th>Net Pay</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    @foreach($rider->payslips as $ps)
                    <tr>
                        <td style="font-size:13px;">{{ $ps->period_label }}</td>
                        <td>{{ $ps->days_worked }}{{ $ps->half_days ? ' + '.$ps->half_days.'×½' : '' }}</td>
                        <td>₱{{ number_format($ps->gross_pay, 2) }}</td>
                        <td style="color:var(--danger);">{{ $ps->cash_advance_deduction > 0 ? '₱'.number_format($ps->cash_advance_deduction,2) : '—' }}</td>
                        <td><strong style="color:var(--success);">₱{{ number_format($ps->net_pay, 2) }}</strong></td>
                        <td>
                            <span class="badge {{ $ps->status === 'final' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($ps->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('payslips.show', $ps) }}" class="btn btn-secondary btn-sm btn-icon">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('payslips.print', $ps) }}" target="_blank" class="btn btn-info btn-sm btn-icon">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
