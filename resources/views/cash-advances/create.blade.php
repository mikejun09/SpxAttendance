@extends('layouts.app')
@section('title', 'Add Cash Advance')
@section('page-title', 'Add Cash Advance')
@section('breadcrumb', 'Cash Advances / Create')

@section('content')
<div style="max-width:520px;">
    <div class="page-header">
        <div><h1>Add Cash Advance</h1></div>
        <a href="{{ route('cash-advances.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
    </div>

    <form method="POST" action="{{ route('cash-advances.store') }}">
        @csrf
        <div class="card">
            <div class="form-group">
                <label>Rider *</label>
                <select name="rider_id" required>
                    <option value="">— Select Rider —</option>
                    @foreach($riders as $r)
                        <option value="{{ $r->id }}"
                            {{ (old('rider_id', $selectedRider?->id) == $r->id) ? 'selected' : '' }}>
                            {{ $r->name }}
                        </option>
                    @endforeach
                </select>
                @error('rider_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-row form-row-2">
                <div class="form-group">
                    <label>Amount (₱) *</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="1" required placeholder="e.g. 500">
                    @error('amount') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date" value="{{ old('date', today()->toDateString()) }}" required onclick="this.showPicker()" style="cursor: pointer;">
                    @error('date') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Reason for cash advance…">
                @error('notes') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div style="display:flex; gap:10px; margin-top:16px;">
            <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save"></i> Record Cash Advance</button>
            <a href="{{ route('cash-advances.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
