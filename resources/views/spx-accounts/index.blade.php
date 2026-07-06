@extends('layouts.app')
@section('title', 'SPX Accounts')
@section('page-title', 'SPX Accounts')
@section('breadcrumb', 'Manage SPX delivery accounts')

@section('content')
<div class="page-header">
    <div><h1>SPX Accounts</h1><p>{{ $accounts->total() }} accounts registered</p></div>
    <a href="{{ route('spx-accounts.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Add Account
    </a>
</div>

<div class="card">
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label>Search</label>
            <input type="text" name="search" placeholder="Code or name…" value="{{ request('search') }}">
        </div>
        <div class="form-group" style="display:flex; gap:8px; align-items:flex-end;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            <a href="{{ route('spx-accounts.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    @if($accounts->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-boxes-stacked"></i>
            <p>No SPX accounts found. <a href="{{ route('spx-accounts.create') }}" style="color:var(--accent)">Add your first account</a>.</p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Account Name</th>
                        <th>Assigned Riders</th>
                        <th>Today (Present)</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $acct)
                    <tr>
                        <td>
                            <a href="{{ route('spx-accounts.show', $acct) }}"
                               style="text-decoration:none;">
                                <span class="badge badge-accent" style="font-family:monospace; font-size:13px; cursor:pointer;">
                                    {{ $acct->account_code }}
                                </span>
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('spx-accounts.show', $acct) }}"
                               style="color:var(--text-primary); text-decoration:none; font-weight:600;">
                                {{ $acct->account_name }}
                            </a>
                        </td>
                        <td>
                            <span style="font-size:20px; font-weight:700; color:{{ ($acct->assigned_riders ?? 0) > 0 ? 'var(--accent)' : 'var(--text-muted)' }}">
                                {{ $acct->assigned_riders ?? 0 }}
                            </span>
                            <span style="font-size:12px; color:var(--text-muted); margin-left:4px;">riders</span>
                        </td>
                        <td>
                            <span style="font-size:18px; font-weight:700; color:{{ ($acct->today_riders ?? 0) > 0 ? 'var(--success)' : 'var(--text-muted)' }}">
                                {{ $acct->today_riders ?? 0 }}
                            </span>
                            <span style="font-size:12px; color:var(--text-muted); margin-left:4px;">present</span>
                        </td>
                        <td>
                            <span class="status-dot {{ $acct->is_active ? 'dot-green' : 'dot-red' }}"></span>
                            {{ $acct->is_active ? 'Active' : 'Inactive' }}
                        </td>
                        <td style="font-size:12px; color:var(--text-muted); max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                            {{ $acct->notes ?? '—' }}
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('spx-accounts.edit', $acct) }}" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('spx-accounts.destroy', $acct) }}" onsubmit="return confirm('Delete {{ $acct->account_code }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $accounts->links() }}</div>
    @endif
</div>
@endsection
