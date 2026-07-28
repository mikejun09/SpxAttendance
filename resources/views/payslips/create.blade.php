@extends('layouts.app')
@section('title', 'Generate Payslip')
@section('page-title', 'Generate Payslip')
@section('breadcrumb', 'Payslips / Create')

@push('styles')
    <style>
        .preview-box {
            background: var(--bg-hover);
            border-radius: 10px;
            padding: 20px;
            margin-top: 16px;
        }

        .preview-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--border);
            font-size: 14px;
        }

        .preview-row:last-child {
            border-bottom: none;
        }

        .preview-row .label {
            color: var(--text-muted);
        }

        .preview-row .value {
            font-weight: 600;
        }

        .preview-total {
            font-size: 20px;
            color: var(--success);
        }

        .ca-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: var(--bg-primary);
            border-radius: 8px;
            margin-bottom: 8px;
            border: 1px solid var(--border);
            transition: border-color .2s;
        }

        .ca-item:hover {
            border-color: var(--accent);
        }

        .ca-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent);
            flex-shrink: 0;
        }

        /* Manual deduction rows */
        .deduction-row {
            display: grid;
            grid-template-columns: 1fr 160px 36px;
            gap: 8px;
            align-items: center;
            padding: 8px 10px;
            background: var(--bg-primary);
            border-radius: 8px;
            margin-bottom: 8px;
            border: 1px solid var(--border);
            transition: border-color .2s;
        }

        .deduction-row:hover {
            border-color: var(--accent);
        }

        .deduction-row input[type="text"],
        .deduction-row input[type="number"] {
            height: 36px;
            padding: 0 10px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 13px;
            width: 100%;
        }

        .deduction-row input[type="text"]:focus,
        .deduction-row input[type="number"]:focus {
            outline: none;
            border-color: var(--accent);
        }

        .btn-remove-deduction {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid var(--danger, #ef4444);
            background: transparent;
            color: var(--danger, #ef4444);
            cursor: pointer;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s, color .2s;
            flex-shrink: 0;
        }

        .btn-remove-deduction:hover {
            background: var(--danger, #ef4444);
            color: #fff;
        }

        .btn-add-deduction {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 7px;
            border: 1.5px dashed var(--accent);
            background: transparent;
            color: var(--accent);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, color .2s;
            margin-top: 4px;
        }

        .btn-add-deduction:hover {
            background: var(--accent);
            color: #fff;
        }

        .deduction-header {
            display: grid;
            grid-template-columns: 1fr 160px 36px;
            gap: 8px;
            padding: 0 10px 6px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--text-muted);
        }

        #live-manual-total {
            transition: color .3s;
        }

        #live-net-pay {
            transition: color .3s;
        }
    </style>
@endpush

@section('content')
    <div style="max-width:780px;">
        <div class="page-header">
            <div>
                <h1>Generate Payslip</h1>
                <p>Select a rider and week period to compute pay.</p>
            </div>
            <a href="{{ route('payslips.index') }}" class="btn btn-secondary"><i class="fa-solid fa-arrow-left"></i> Back</a>
        </div>

        {{-- Step 1: Select rider + week --}}
        <div class="card" style="margin-bottom:20px;">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="fa-solid fa-magnifying-glass" style="color:var(--accent)"></i> Select Rider &amp; Period
            </div>
            <form method="GET" action="{{ route('payslips.create') }}"
                style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
                <div class="form-group" style="flex:1; min-width:200px; margin:0;">
                    <label>Rider *</label>
                    <select name="rider_id" required>
                        <option value="">— Select Rider —</option>
                        @foreach ($riders as $r)
                            <option value="{{ $r->id }}" {{ $selectedRider?->id == $r->id ? 'selected' : '' }}>
                                {{ $r->name }} (₱{{ number_format($r->daily_rate, 2) }}/day)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex:1; min-width:180px; margin:0;">
                    <label>Week Starting (Monday) *</label>
                    <div class="date-picker full">
                        <i class="fa-solid fa-calendar-week dp-icon"></i>
                        <input type="date" name="week_start" value="{{ $weekStart->toDateString() }}"
                            title="Select the Monday of the pay week" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-calculator"></i> Preview
                </button>
            </form>
        </div>

        @if ($selectedRider && $preview)
            {{-- Step 2: Review & generate --}}
            <form method="POST" action="{{ route('payslips.store') }}" id="payslipForm">
                @csrf
                <input type="hidden" name="rider_id" value="{{ $selectedRider->id }}">
                <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">

                {{-- Hidden fields for live preview data --}}
                <input type="hidden" id="js-gross" value="{{ $preview['gross_pay'] }}">
                <input type="hidden" id="js-prior-balance" value="{{ $preview['carried_balance'] }}">

                <div class="card" style="margin-bottom:20px;">
                    <div class="card-title" style="margin-bottom:16px;">
                        <i class="fa-solid fa-chart-bar" style="color:var(--accent)"></i> Attendance Summary
                    </div>
                    <div style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">
                        <strong>{{ $selectedRider->name }}</strong> &bull;
                        Week of {{ $weekStart->format('M d') }} – {{ $weekEnd->format('M d, Y') }}
                    </div>

                    @if ($preview['total_days'] == 0)
                        <div class="alert alert-danger">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <div>
                                <strong>Cannot generate payslip:</strong> This rider is marked as absent or rest day for the entire week (0 billable days).
                            </div>
                        </div>
                    @else
                        <div class="table-wrap" style="margin-bottom:16px;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Day</th>
                                        <th>SPX Account</th>
                                        <th>Status</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($preview['attendances']->sortBy('date') as $att)
                                        <tr>
                                            <td style="font-size:13px;">{{ $att->date->format('M d, Y') }}</td>
                                            <td style="color:var(--text-muted); font-size:12px;">
                                                {{ $att->date->format('l') }}</td>
                                            <td>
                                                @if ($att->spxAccount)
                                                    <span
                                                        class="badge badge-accent">{{ $att->spxAccount->account_code }}</span>
                                                @else
                                                    <span style="color:var(--text-muted)">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $bc = match ($att->status) {
                                                        'present' => 'badge-success',
                                                        'absent' => 'badge-danger',
                                                        'half_day' => 'badge-warning',
                                                        default => 'badge-muted',
                                                    };
                                                @endphp
                                                <span class="badge {{ $bc }}">{{ $att->status_label }}</span>
                                            </td>
                                            <td style="color:var(--text-muted); font-size:12px;">{{ $att->notes ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <div class="preview-box">
                        <div class="preview-row">
                            <span class="label">Full Days Present</span>
                            <span class="value">{{ $preview['days_worked'] }} days</span>
                        </div>

                        <div class="preview-row">
                            <span class="label">Total Billable Days</span>
                            <span class="value">{{ $preview['total_days'] }} days</span>
                        </div>
                        <div class="preview-row">
                            <span class="label">Daily Rate</span>
                            <span class="value">₱{{ number_format($selectedRider->daily_rate, 2) }}</span>
                        </div>
                        <div class="preview-row" style="font-size:16px; padding-top:12px;">
                            <span class="label"><strong>Gross Pay</strong></span>
                            <span class="value preview-total">₱{{ number_format($preview['gross_pay'], 2) }}</span>
                        </div>

                        @if ($preview['carried_balance'] > 0)
                            <div class="preview-row" style="background:rgba(239,68,68,.08); border-radius:6px; padding:10px 12px; margin-top:8px;">
                                <span class="label" style="color:var(--danger,#ef4444); font-weight:600;">
                                    <i class="fa-solid fa-triangle-exclamation"></i> Prior Balance Carried Forward
                                </span>
                                <span class="value" style="color:var(--danger,#ef4444);">–₱{{ number_format($preview['carried_balance'], 2) }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Cash Advances --}}
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-title" style="margin-bottom:12px;">
                        <i class="fa-solid fa-money-bill-wave" style="color:var(--accent)"></i> Cash Advance Deductions
                    </div>

                    @if ($preview['pending_ca']->isEmpty())
                        <div style="color:var(--text-muted); font-size:13px;">
                            <i class="fa-solid fa-check-circle" style="color:var(--success)"></i>
                            No pending cash advances for this rider.
                        </div>
                    @else
                        <p style="font-size:13px; color:var(--text-muted); margin-bottom:12px;">
                            Select cash advances to deduct from this payslip:
                        </p>
                        @foreach ($preview['pending_ca'] as $ca)
                            <div class="ca-item">
                                <input type="checkbox" name="cash_advance_ids[]" id="ca_{{ $ca->id }}"
                                    value="{{ $ca->id }}" data-amount="{{ $ca->amount }}" checked
                                    class="ca-checkbox">
                                <label for="ca_{{ $ca->id }}"
                                    style="flex:1; cursor:pointer; margin:0; display:flex; justify-content:space-between;">
                                    <span>
                                        <strong style="color:var(--warning);">₱{{ number_format($ca->amount, 2) }}</strong>
                                        <span style="color:var(--text-muted); font-size:12px; margin-left:8px;">
                                            {{ $ca->date->format('M d, Y') }} {{ $ca->notes ? '— ' . $ca->notes : '' }}
                                        </span>
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    @endif
                </div>

                {{-- ─── Additional Rate / Pay & Allowances ────────────────────── --}}
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-title" style="margin-bottom:4px;">
                        <i class="fa-solid fa-circle-plus" style="color:var(--success, #22c55e)"></i> Additional Pay / Rate &amp; Allowances
                    </div>
                    <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
                        Add any additional rate, incentives, or allowances for this week (e.g. ₱150 bonus, gas allowance).
                    </p>

                    <div id="additions-container">
                        {{-- Rows injected dynamically --}}
                    </div>

                    <button type="button" class="btn-add-deduction" id="btn-add-addition" style="border-color:var(--success, #22c55e); color:var(--success, #22c55e);">
                        <i class="fa-solid fa-plus"></i> Add Additional Rate / Allowance
                    </button>

                    {{-- Live summary --}}
                    <div id="manual-addition-summary"
                        style="display:none; margin-top:14px; padding:10px 14px; background:var(--bg-hover); border-radius:8px; font-size:13px;">
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-muted);">Total Additional Pay</span>
                            <span id="live-addition-total"
                                style="font-weight:700; color:var(--success, #22c55e);">₱0.00</span>
                        </div>
                    </div>
                </div>

                {{-- ─── Manual Salary Deductions ─────────────────────────────── --}}
                <div class="card" style="margin-bottom:20px;">
                    <div class="card-title" style="margin-bottom:4px;">
                        <i class="fa-solid fa-scissors" style="color:var(--danger, #ef4444)"></i> Manual Salary Deductions
                    </div>
                    <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">
                        Add any one-off deductions (e.g. uniform fee, tardiness penalty, tool damage).
                    </p>

                    <div id="deductions-container">
                        {{-- Rows injected dynamically --}}
                    </div>

                    <button type="button" class="btn-add-deduction" id="btn-add-deduction">
                        <i class="fa-solid fa-plus"></i> Add Deduction
                    </button>

                    {{-- Live summary --}}
                    <div id="manual-deduction-summary"
                        style="display:none; margin-top:14px; padding:10px 14px; background:var(--bg-hover); border-radius:8px; font-size:13px;">
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-muted);">Total Manual Deductions</span>
                            <span id="live-manual-total"
                                style="font-weight:700; color:var(--danger, #ef4444);">₱0.00</span>
                        </div>
                    </div>
                </div>

                {{-- Live Net Pay Preview --}}
                <div class="card"
                    style="margin-bottom:20px; border: 2px solid var(--accent); background: var(--bg-hover);">
                    <div
                        style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                        <div>
                            <div
                                style="font-size:12px; font-weight:700; letter-spacing:.5px; text-transform:uppercase; color:var(--text-muted); margin-bottom:4px;">
                                <i class="fa-solid fa-calculator"></i> Estimated Net Pay
                            </div>
                            <div style="font-size:11px; color:var(--text-muted);">
                                (Gross + Additional Pay) − CA Deductions − Manual Deductions
                            </div>
                        </div>
                        <div id="live-net-pay" style="font-size:28px; font-weight:800; color:var(--success);">
                            ₱{{ number_format(max(0, $preview['gross_pay'] - $preview['carried_balance']), 2) }}
                        </div>
                    </div>
                </div>

                {{-- Final Options --}}
                <div class="card" style="margin-bottom:20px;">
                    <div class="form-row form-row-2">
                        <div class="form-group">
                            <label>Payslip Status</label>
                            <select name="status" required>
                                <option value="final">Final</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="Any notes for this payslip…"></textarea>
                    </div>
                </div>

                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn btn-primary" style="padding:12px 28px; font-size:15px;" {{ $preview['total_days'] == 0 ? 'disabled' : '' }}>
                        <i class="fa-solid fa-file-invoice-dollar"></i> Generate Payslip
                    </button>
                    <a href="{{ route('payslips.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        @endif
    </div>

    @push('scripts')
        <script>
            (function() {
                const gross = parseFloat(document.getElementById('js-gross')?.value ?? 0);
                const deductionsContainer = document.getElementById('deductions-container');
                const additionsContainer = document.getElementById('additions-container');
                const btnAddDeduction = document.getElementById('btn-add-deduction');
                const btnAddAddition = document.getElementById('btn-add-addition');
                const deductionSummary = document.getElementById('manual-deduction-summary');
                const additionSummary = document.getElementById('manual-addition-summary');
                const liveManual = document.getElementById('live-manual-total');
                const liveAddition = document.getElementById('live-addition-total');
                const liveNet = document.getElementById('live-net-pay');

                // CA checkboxes
                let caTotal = 0;

                function sumCa() {
                    caTotal = 0;
                    document.querySelectorAll('.ca-checkbox:checked').forEach(cb => {
                        caTotal += parseFloat(cb.dataset.amount || 0);
                    });
                    refreshAll();
                }
                document.querySelectorAll('.ca-checkbox').forEach(cb => cb.addEventListener('change', sumCa));
                sumCa(); // initial

                let deductionRowIndex = 0;
                let additionRowIndex = 0;

                // Add Addition Row
                function addAdditionRow(label = '', amount = '') {
                    const idx = additionRowIndex++;
                    const row = document.createElement('div');
                    row.className = 'deduction-row';
                    row.dataset.idx = idx;
                    row.innerHTML = `
                        <input type="text"   name="additions[${idx}][label]"  placeholder="e.g. Additional Rate / Incentive" value="${label}" required>
                        <input type="number" name="additions[${idx}][amount]" placeholder="0.00" min="0" step="0.01" value="${amount}" required>
                        <button type="button" class="btn-remove-deduction" title="Remove">✕</button>
                    `;
                    row.querySelector('.btn-remove-deduction').addEventListener('click', () => {
                        row.remove();
                        refreshAll();
                    });
                    row.querySelectorAll('input').forEach(inp => inp.addEventListener('input', refreshAll));
                    additionsContainer.appendChild(row);

                    if (additionsContainer.children.length === 1) showAdditionHeader();
                    refreshAll();
                }

                function showAdditionHeader() {
                    if (document.getElementById('addition-header')) return;
                    const h = document.createElement('div');
                    h.id = 'addition-header';
                    h.className = 'deduction-header';
                    h.innerHTML = '<span>Remarks / Description</span><span>Amount (₱)</span><span></span>';
                    additionsContainer.before(h);
                }

                // Add Deduction Row
                function addDeductionRow(label = '', amount = '') {
                    const idx = deductionRowIndex++;
                    const row = document.createElement('div');
                    row.className = 'deduction-row';
                    row.dataset.idx = idx;
                    row.innerHTML = `
                        <input type="text"   name="deductions[${idx}][label]"  placeholder="e.g. Uniform Fee" value="${label}" required>
                        <input type="number" name="deductions[${idx}][amount]" placeholder="0.00" min="0" step="0.01" value="${amount}" required>
                        <button type="button" class="btn-remove-deduction" title="Remove">✕</button>
                    `;
                    row.querySelector('.btn-remove-deduction').addEventListener('click', () => {
                        row.remove();
                        refreshAll();
                    });
                    row.querySelectorAll('input').forEach(inp => inp.addEventListener('input', refreshAll));
                    deductionsContainer.appendChild(row);

                    if (deductionsContainer.children.length === 1) showDeductionHeader();
                    refreshAll();
                }

                function showDeductionHeader() {
                    if (document.getElementById('deduction-header')) return;
                    const h = document.createElement('div');
                    h.id = 'deduction-header';
                    h.className = 'deduction-header';
                    h.innerHTML = '<span>Description</span><span>Amount (₱)</span><span></span>';
                    deductionsContainer.before(h);
                }

                function refreshAll() {
                    // Headers removal check
                    const addHeader = document.getElementById('addition-header');
                    if (addHeader && additionsContainer.children.length === 0) addHeader.remove();

                    const dedHeader = document.getElementById('deduction-header');
                    if (dedHeader && deductionsContainer.children.length === 0) dedHeader.remove();

                    // Sum additions
                    let addTotal = 0;
                    additionsContainer.querySelectorAll('.deduction-row').forEach(row => {
                        addTotal += parseFloat(row.querySelector('input[type="number"]').value) || 0;
                    });

                    if (addTotal > 0 || additionsContainer.children.length > 0) {
                        additionSummary.style.display = 'block';
                        liveAddition.textContent = '₱' + addTotal.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    } else {
                        additionSummary.style.display = 'none';
                    }

                    // Sum deductions
                    let dedTotal = 0;
                    deductionsContainer.querySelectorAll('.deduction-row').forEach(row => {
                        dedTotal += parseFloat(row.querySelector('input[type="number"]').value) || 0;
                    });

                    if (dedTotal > 0 || deductionsContainer.children.length > 0) {
                        deductionSummary.style.display = 'block';
                        liveManual.textContent = '₱' + dedTotal.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    } else {
                        deductionSummary.style.display = 'none';
                    }

                    const priorBalance = parseFloat(document.getElementById('js-prior-balance')?.value ?? 0);
                    const net = Math.max(0, (gross + addTotal) - caTotal - dedTotal - priorBalance);
                    liveNet.textContent = '₱' + net.toLocaleString('en-PH', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    liveNet.style.color = net > 0 ? 'var(--success)' : 'var(--danger, #ef4444)';
                }

                btnAddAddition?.addEventListener('click', () => addAdditionRow());
                btnAddDeduction?.addEventListener('click', () => addDeductionRow());
            })();
        </script>
    @endpush
@endsection
