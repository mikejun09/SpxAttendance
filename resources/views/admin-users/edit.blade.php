@extends('layouts.app')
@section('title', 'Edit Admin')
@section('page-title', 'Edit Admin')
@section('breadcrumb', 'Admin Users / Edit')

@section('content')
<div style="max-width:680px;">
    <div class="page-header">
        <div><h1>Edit Admin User</h1><p>Update details for {{ $adminUser->name }}.</p></div>
        <a href="{{ route('admin-users.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>

    <form method="POST" action="{{ route('admin-users.update', $adminUser) }}">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-title" style="margin-bottom:20px;">
                <i class="fa-solid fa-user-shield" style="color:var(--accent)"></i> Administrator Details
            </div>

            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="{{ old('name', $adminUser->name) }}" required>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" value="{{ old('email', $adminUser->email) }}" required>
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Changes</button>
            <a href="{{ route('admin-users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>

    <div class="card" style="margin-top:24px; border-color:rgba(239,68,68,0.3);">
        <div class="card-title" style="margin-bottom:12px; color:var(--danger);">
            <i class="fa-solid fa-key"></i> Reset Password
        </div>
        <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">
            Reset this administrator's password to the default value: <strong>"password"</strong>.
        </p>
        <form method="POST" action="{{ route('admin-users.reset-password', $adminUser) }}" onsubmit="return confirm('Are you sure you want to reset the password for {{ $adminUser->name }} to &quot;password&quot;?')">
            @csrf
            <button type="submit" class="btn btn-danger"><i class="fa-solid fa-rotate-left"></i> Reset Password to "password"</button>
        </form>
    </div>
</div>
@endsection
