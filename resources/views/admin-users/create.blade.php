@extends('layouts.app')
@section('title', 'Add Admin')
@section('page-title', 'Add Admin')
@section('breadcrumb', 'Admin Users / Create')

@section('content')
<div style="max-width:680px;">
    <div class="page-header">
        <div><h1>Add New Admin</h1><p>Fill in the details to create a new administrator account.</p></div>
        <a href="{{ route('admin-users.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>

    <form method="POST" action="{{ route('admin-users.store') }}">
        @csrf

        <div class="card">
            <div class="card-title" style="margin-bottom:20px;">
                <i class="fa-solid fa-user-shield" style="color:var(--accent)"></i> Administrator Details
            </div>

            <div class="form-group">
                <label>Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Administrator Name">
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label>Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@example.com">
                @error('email') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Password *</label>
                    <input type="password" name="password" required placeholder="Min. 8 characters">
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="password_confirmation" required placeholder="Confirm password">
                    @error('password_confirmation') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Create Admin</button>
            <a href="{{ route('admin-users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
