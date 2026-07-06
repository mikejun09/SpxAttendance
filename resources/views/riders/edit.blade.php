@extends('layouts.app')
@section('title', 'Edit ' . $rider->name)
@section('page-title', 'Edit Rider')
@section('breadcrumb', 'Riders / ' . $rider->name . ' / Edit')

@section('content')
<div style="max-width:680px;">
    <div class="page-header">
        <div><h1>Edit Rider</h1><p>Update {{ $rider->name }}'s information.</p></div>
        <a href="{{ route('riders.show', $rider) }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>

    <form method="POST" action="{{ route('riders.update', $rider) }}">
        @csrf @method('PATCH')

        <div class="card">
            <div class="card-title" style="margin-bottom:20px;">
                <i class="fa-solid fa-user" style="color:var(--accent)"></i> Rider Information
            </div>

            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $rider->name) }}" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Employee ID</label>
                    <input type="text" name="employee_id" value="{{ old('employee_id', $rider->employee_id) }}">
                    @error('employee_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="tel" name="contact_number" value="{{ old('contact_number', $rider->contact_number) }}">
                    @error('contact_number') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Daily Rate (₱) *</label>
                    <input type="number" name="daily_rate" value="{{ old('daily_rate', $rider->daily_rate) }}" step="0.01" min="0" required>
                    @error('daily_rate') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $rider->is_active) ? 'checked' : '' }}>
                    Active Rider
                </label>
            </div>

            @if($rider->user)
            <div class="alert alert-info">
                <i class="fa-solid fa-circle-info"></i>
                Portal login linked: <strong>{{ $rider->user->email }}</strong>. To change password, the rider should update it from their profile.
            </div>
            @endif
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Changes</button>
            <a href="{{ route('riders.show', $rider) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
