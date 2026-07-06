@extends('layouts.app')
@section('title', 'Add SPX Account')
@section('page-title', 'Add SPX Account')
@section('breadcrumb', 'SPX Accounts / Create')

@section('content')
<div style="max-width:560px;">
    <div class="page-header">
        <div><h1>Add SPX Account</h1></div>
        <a href="{{ route('spx-accounts.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>

    <form method="POST" action="{{ route('spx-accounts.store') }}">
        @csrf
        <div class="card">
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Account Code *</label>
                    <input type="text" name="account_code" value="{{ old('account_code') }}" required placeholder="e.g. SPX-001">
                    @error('account_code') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Account Name *</label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}" required placeholder="e.g. Quezon City Hub">
                    @error('account_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3" placeholder="Optional notes…">{{ old('notes') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                    Active Account
                </label>
            </div>
        </div>
        <div style="display:flex; gap:10px; margin-top:16px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Create Account</button>
            <a href="{{ route('spx-accounts.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
