@extends('layouts.app')
@section('title', 'Payslips')
@section('page-title', 'Payslips')
@section('breadcrumb', 'Weekly payroll records')

@section('content')
<div class="page-header">
    <div><h1>Payslips</h1><p>{{ $payslips->total() }} total payslips</p></div>
    @if(auth()->user()->isAdmin())
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <a href="{{ route('payslips.report') }}" target="_blank" class="btn btn-secondary">
            <i class="fa-solid fa-print"></i> Print Report
        </a>
        <a href="{{ route('payslips.bulk-cutoff') }}" class="btn btn-info">
            <i class="fa-solid fa-bolt"></i> Weekly Cut-off
        </a>
        <a href="{{ route('payslips.create') }}" class="btn btn-primary">
            <i class="fa-solid fa-file-circle-plus"></i> Generate Payslip
        </a>
    </div>
    @endif
</div>

<div class="card">
    @if(auth()->user()->isAdmin())
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label>Rider</label>
            <select name="rider_id">
                <option value="">All Riders</option>
                @foreach($riders as $r)
                    <option value="{{ $r->id }}" {{ request('rider_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="final" {{ request('status') === 'final' ? 'selected' : '' }}>Final</option>
            </select>
        </div>
        <div class="form-group" style="display:flex; gap:8px; align-items:flex-end;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
            <a href="{{ route('payslips.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
    @endif

    @if($payslips->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-file-circle-question"></i>
            <p>No payslips found. @if(auth()->user()->isAdmin())<a href="{{ route('payslips.create') }}" style="color:var(--accent)">Generate the first one</a>.@endif</p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        @if(auth()->user()->isAdmin())<th>Rider</th>@endif
                        <th>Week Period</th>
                        <th>Days Worked</th>
                        <th>Daily Rate</th>
                        <th>Gross Pay</th>
                        <th>CA Deduction</th>
                        <th>Net Pay</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payslips as $ps)
                    <tr>
                        @if(auth()->user()->isAdmin())
                        <td>
                            <a href="{{ route('riders.show', $ps->rider) }}" style="color:var(--text-primary); text-decoration:none; font-weight:500;">
                                {{ $ps->rider->name }}
                            </a>
                        </td>
                        @endif
                        <td>
                            <div style="font-weight:500;">{{ $ps->period_label }}</div>
                            <div style="font-size:11px; color:var(--text-muted);">{{ $ps->week_start->format('D') }} – {{ $ps->week_end->format('D') }}</div>
                        </td>
                        <td>
                            {{ $ps->days_worked }}{{ $ps->half_days ? ' + '.$ps->half_days.'×½' : '' }}
                            <div style="font-size:11px; color:var(--text-muted);">days</div>
                        </td>
                        <td style="color:var(--text-muted);">₱{{ number_format($ps->daily_rate, 2) }}</td>
                        <td>₱{{ number_format($ps->gross_pay, 2) }}</td>
                        <td style="color:{{ $ps->cash_advance_deduction > 0 ? 'var(--danger)' : 'var(--text-muted)' }};">
                            {{ $ps->cash_advance_deduction > 0 ? '₱'.number_format($ps->cash_advance_deduction,2) : '—' }}
                        </td>
                        <td><strong style="color:var(--success); font-size:15px;">₱{{ number_format($ps->net_pay, 2) }}</strong></td>
                        <td>
                            <span class="badge {{ $ps->status === 'final' ? 'badge-success' : 'badge-warning' }}">
                                {{ ucfirst($ps->status) }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('payslips.show', $ps) }}" class="btn btn-secondary btn-sm btn-icon" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('payslips.print', $ps) }}" target="_blank" class="btn btn-info btn-sm btn-icon" title="Print">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                @if(auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('payslips.destroy', $ps) }}" onsubmit="return confirm('Delete this payslip? Cash advances will be restored.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $payslips->links() }}</div>
    @endif
</div>
@endsection
