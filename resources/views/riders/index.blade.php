@extends('layouts.app')
@section('title', 'Riders')
@section('page-title', 'Riders')
@section('breadcrumb', 'Manage all riders')

@section('content')
<div class="page-header">
    <div>
        <h1>Riders</h1>
        <p>{{ $riders->total() }} total riders</p>
    </div>
    <a href="{{ route('riders.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-plus"></i> Add Rider
    </a>
</div>

<div class="card">
    <form method="GET" class="filter-bar">
        <div class="form-group">
            <label>Search</label>
            <input type="text" name="search" placeholder="Name or employee ID…" value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status">
                <option value="">All</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="form-group" style="display:flex; gap:8px; align-items:flex-end;">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-magnifying-glass"></i> Search
            </button>
            <a href="{{ route('riders.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    @if($riders->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-user-slash"></i>
            <p>No riders found. <a href="{{ route('riders.create') }}" style="color:var(--accent)">Add your first rider</a>.</p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Rider</th>
                        <th>Employee ID</th>
                        <th>Contact</th>
                        <th>Daily Rate</th>
                        <th>Portal Access</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($riders as $rider)
                    <tr>
                        <td>
                            <a href="{{ route('riders.show', $rider) }}" style="color:var(--text-primary); text-decoration:none;">
                                <strong>{{ $rider->name }}</strong>
                            </a>
                        </td>
                        <td style="color:var(--text-muted); font-size:12px; font-family:monospace;">
                            {{ $rider->employee_id ?? '—' }}
                        </td>
                        <td style="color:var(--text-muted); font-size:13px;">{{ $rider->contact_number ?? '—' }}</td>
                        <td><strong style="color:var(--accent);">₱{{ number_format($rider->daily_rate, 2) }}</strong></td>
                        <td>
                            @if($rider->user_id)
                                <span class="badge badge-success"><i class="fa-solid fa-check"></i> Yes</span>
                            @else
                                <span class="badge badge-muted">No</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-dot {{ $rider->is_active ? 'dot-green' : 'dot-red' }}"></span>
                            {{ $rider->is_active ? 'Active' : 'Inactive' }}
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('riders.show', $rider) }}" class="btn btn-secondary btn-sm btn-icon" title="View">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('riders.edit', $rider) }}" class="btn btn-secondary btn-sm btn-icon" title="Edit">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('riders.destroy', $rider) }}" onsubmit="return confirm('Delete {{ $rider->name }}?')">
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

        <div class="pagination">
            {{ $riders->links() }}
        </div>
    @endif
</div>
@endsection
