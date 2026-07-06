@extends('layouts.app')
@section('title', 'Cash Advances')
@section('page-title', 'Cash Advances')
@section('breadcrumb', 'Manage rider cash advances')

@section('content')
<div class="page-header">
    <div><h1>Cash Advances</h1><p>{{ $cashAdvances->total() }} records</p></div>
    <a href="{{ route('cash-advances.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Add Cash Advance
    </a>
</div>

<div class="card">
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
                <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                <option value="deducted" {{ request('status') === 'deducted' ? 'selected' : '' }}>Deducted</option>
            </select>
        </div>
        <div class="form-group" style="display:flex; gap:8px; align-items:flex-end;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
            <a href="{{ route('cash-advances.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    @if($cashAdvances->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-hand-holding-dollar"></i>
            <p>No cash advances found.</p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rider</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cashAdvances as $ca)
                    <tr>
                        <td>
                            <a href="{{ route('riders.show', $ca->rider) }}" style="color:var(--text-primary); text-decoration:none; font-weight:500;">
                                {{ $ca->rider->name }}
                            </a>
                        </td>
                        <td style="font-size:13px; color:var(--text-muted);">{{ $ca->date->format('M d, Y') }}</td>
                        <td><strong style="color:var(--warning); font-size:16px;">₱{{ number_format($ca->amount, 2) }}</strong></td>
                        <td style="font-size:12px; color:var(--text-muted);">{{ $ca->notes ?? '—' }}</td>
                        <td>
                            @if($ca->is_deducted)
                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> Deducted</span>
                            @else
                                <span class="badge badge-warning"><i class="fa-solid fa-clock"></i> Pending</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('cash-advances.destroy', $ca) }}" onsubmit="return confirm('{{ $ca->is_deducted ? 'WARNING: This cash advance of ₱' . number_format($ca->amount, 2) . ' for ' . $ca->rider->name . ' has already been deducted from a payslip. Deleting it will automatically update the payslip\'s deductions and net pay. Are you sure you want to delete it?' : 'Are you sure you want to delete this cash advance of ₱' . number_format($ca->amount, 2) . ' for ' . $ca->rider->name . '?' }}')">
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
        <div class="pagination">{{ $cashAdvances->links() }}</div>
    @endif
</div>
@endsection
