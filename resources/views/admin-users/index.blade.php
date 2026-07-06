@extends('layouts.app')
@section('title', 'Admin Users')
@section('page-title', 'Admin Users')
@section('breadcrumb', 'Manage all administrators')

@section('content')
<div class="page-header">
    <div>
        <h1>Admin Users</h1>
        <p>{{ $admins->total() }} total administrators</p>
    </div>
    <a href="{{ route('admin-users.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-user-shield"></i> Add Admin
    </a>
</div>

<div class="card">
    @if($admins->isEmpty())
        <div class="empty-state">
            <i class="fa-solid fa-user-slash"></i>
            <p>No admin users found.</p>
        </div>
    @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($admins as $admin)
                    <tr>
                        <td>
                            <strong>{{ $admin->name }}</strong>
                            @if($admin->id === auth()->id())
                                <span class="badge badge-accent" style="margin-left: 6px;">You</span>
                            @endif
                        </td>
                        <td style="color:var(--text-muted);">{{ $admin->email }}</td>
                        <td style="color:var(--text-muted); font-size:13px;">{{ $admin->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <div style="display:flex; gap:6px; align-items:center;">
                                <a href="{{ route('admin-users.edit', $admin) }}" class="btn btn-secondary btn-sm" title="Edit">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('admin-users.reset-password', $admin) }}" onsubmit="return confirm('Reset password for {{ $admin->name }} to &quot;password&quot;?')" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm" title="Reset Password">
                                        <i class="fa-solid fa-rotate-left"></i> Reset Password
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($admins->hasPages())
            <div class="pagination">
                {{ $admins->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
