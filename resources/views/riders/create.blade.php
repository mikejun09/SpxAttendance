@extends('layouts.app')
@section('title', 'Add Rider')
@section('page-title', 'Add Rider')
@section('breadcrumb', 'Riders / Create')

@section('content')
<div style="max-width:680px;">
    <div class="page-header">
        <div><h1>Add New Rider</h1><p>Fill in the rider details below.</p></div>
        <a href="{{ route('riders.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>

    <form method="POST" action="{{ route('riders.store') }}">
        @csrf

        <div class="card">
            <div class="card-title" style="margin-bottom:20px;">
                <i class="fa-solid fa-user" style="color:var(--accent)"></i> Rider Information
            </div>

            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Juan dela Cruz">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Employee ID</label>
                    <input type="text" name="employee_id" value="{{ old('employee_id') }}" placeholder="e.g. R-0001">
                    @error('employee_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="tel" name="contact_number" value="{{ old('contact_number') }}" placeholder="09XXXXXXXXX">
                    @error('contact_number') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Daily Rate (₱) *</label>
                    <input type="number" name="daily_rate" value="{{ old('daily_rate', '500') }}" step="0.01" min="0" required>
                    @error('daily_rate') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    Active Rider
                </label>
            </div>
        </div>

        <div class="card" style="margin-top:16px;">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="fa-solid fa-lock" style="color:var(--accent)"></i> Portal Login (Optional)
            </div>
            <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">
                Create a login account so this rider can access the rider portal to view their attendance and payslips.
            </p>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="create_account" id="createAccount" value="1" {{ old('create_account') ? 'checked' : '' }}>
                    Create portal login for this rider
                </label>
            </div>

            <div id="accountFields" style="display:{{ old('create_account') ? 'block' : 'none' }};">
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="rider@example.com">
                        @error('email') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" placeholder="Min. 6 characters">
                        @error('password') <div class="form-error">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Create Rider</button>
            <a href="{{ route('riders.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('createAccount').addEventListener('change', function() {
        document.getElementById('accountFields').style.display = this.checked ? 'block' : 'none';
    });
</script>
@endpush
@endsection
