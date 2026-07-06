@extends('layouts.app')
@section('title', 'Edit SPX Account')
@section('page-title', 'Edit SPX Account')
@section('breadcrumb', 'SPX Accounts / Edit')

@section('content')
<div style="max-width:560px;">
    <div class="page-header">
        <div><h1>Edit SPX Account</h1><p>{{ $spxAccount->account_code }}</p></div>
        <a href="{{ route('spx-accounts.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>

    <form method="POST" action="{{ route('spx-accounts.update', $spxAccount) }}">
        @csrf @method('PATCH')
        <div class="card">
            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Account Code *</label>
                    <input type="text" name="account_code" value="{{ old('account_code', $spxAccount->account_code) }}" required>
                    @error('account_code') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Account Name *</label>
                    <input type="text" name="account_name" value="{{ old('account_name', $spxAccount->account_name) }}" required>
                    @error('account_name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" rows="3">{{ old('notes', $spxAccount->notes) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $spxAccount->is_active) ? 'checked' : '' }}>
                    Active Account
                </label>
            </div>
        </div>
        <div style="display:flex; gap:10px; margin-top:16px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Save Changes</button>
            <a href="{{ route('spx-accounts.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
