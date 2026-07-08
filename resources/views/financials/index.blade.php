@extends('layouts.app')

@section('title', 'Finance Management')
@section('page-title', 'Finance Management')
@section('breadcrumb', 'Input and track company expenses and weekly income')

@push('styles')
    <style>
        .finance-grid {
            display: grid;
            grid-template-columns: 340px 1fr;
            gap: 20px;
            align-items: start;
        }

        .panel-header-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 12px;
        }

        .tab-btn {
            padding: 9px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-secondary);
            transition: all .2s;
            font-family: inherit;
        }

        .tab-btn.active {
            background: rgba(249, 115, 22, 0.15);
            color: var(--accent);
            border-color: rgba(249, 115, 22, 0.3);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        .amount-display {
            font-size: 16px;
            font-weight: 700;
            color: var(--accent);
        }

        .income-amount {
            color: var(--success);
        }

        .expense-amount {
            color: var(--danger);
        }

        @media (max-width: 992px) {
            .finance-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="panel-header-tabs">
        <button class="tab-btn active" id="btn-expenses" onclick="switchTab('expenses')">
            <i class="fa-solid fa-file-invoice-dollar"></i> Expenses
        </button>
        <button class="tab-btn" id="btn-income" onclick="switchTab('income')">
            <i class="fa-solid fa-hand-holding-dollar"></i> Weekly Income
        </button>
        <button class="tab-btn" id="btn-balances" onclick="switchTab('balances')">
            <i class="fa-solid fa-scale-unbalanced"></i> Outstanding Balances
        </button>
    </div>

    {{-- Tab: Expenses ────────────────────────────────────────── --}}
    <div id="tab-expenses" class="tab-panel active">
        <div class="finance-grid">
            {{-- Add Expense Form --}}
            <div class="card">
                <div class="card-header" style="margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                    <div class="card-title"><i class="fa-solid fa-plus-circle" style="color:var(--danger)"></i> Add Expense</div>
                </div>
                <form method="POST" action="{{ route('financials.store_expense') }}">
                    @csrf
                    <div class="form-group">
                        <label for="expense_amount">Amount (₱) *</label>
                        <input type="number" step="0.01" name="amount" id="expense_amount" placeholder="0.00" required>
                    </div>

                    <div class="form-group">
                        <label for="expense_date">Date *</label>
                        <div class="date-picker full">
                            <i class="fa-solid fa-calendar-day dp-icon"></i>
                            <input type="date" name="date" id="expense_date" value="{{ today()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="expense_desc">Description *</label>
                        <textarea name="description" id="expense_desc" rows="3" placeholder="e.g. Office rent, Electricity bill, fuel..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                        <i class="fa-solid fa-save"></i> Save Expense
                    </button>
                </form>
            </div>

            {{-- Expenses Table --}}
            <div class="card">
                <div class="card-header" style="margin-bottom:15px;">
                    <div class="card-title"><i class="fa-solid fa-list" style="color:var(--accent)"></i> Expense History</div>
                </div>

                @if($expenses->isEmpty())
                    <div class="empty-state" style="padding:40px 20px;">
                        <i class="fa-solid fa-receipt"></i>
                        <p>No expenses recorded yet.</p>
                    </div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th style="width: 80px; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->date->format('M d, Y') }}</td>
                                        <td>{{ $expense->description }}</td>
                                        <td class="amount-display expense-amount">₱{{ number_format($expense->amount, 2) }}</td>
                                        <td style="text-align: center;">
                                            <form method="POST" action="{{ route('financials.destroy_expense', $expense) }}" onsubmit="return confirm('Are you sure you want to delete this expense record?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab: Weekly Income ────────────────────────────────────── --}}
    <div id="tab-income" class="tab-panel">
        <div class="finance-grid">
            {{-- Add Weekly Income Form --}}
            <div class="card">
                <div class="card-header" style="margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid var(--border);">
                    <div class="card-title"><i class="fa-solid fa-plus-circle" style="color:var(--success)"></i> Add Weekly Income</div>
                </div>
                <form method="POST" action="{{ route('financials.store_income') }}">
                    @csrf
                    <div class="form-group">
                        <label for="income_amount">Amount (₱) *</label>
                        <input type="number" step="0.01" name="amount" id="income_amount" placeholder="0.00" required>
                    </div>

                    <div class="form-group">
                        <label for="income_week">Date (Any day of the target week) *</label>
                        <div class="date-picker full">
                            <i class="fa-solid fa-calendar-week dp-icon"></i>
                            <input type="date" name="week_start" id="income_week" value="{{ today()->toDateString() }}" required>
                        </div>
                        <small style="font-size:11px; color:var(--text-muted); display:block; margin-top:5px;">
                            The system will automatically align this to the starting Monday of the week.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="income_notes">Notes / Source</label>
                        <textarea name="notes" id="income_notes" rows="3" placeholder="e.g. Hub delivery payouts, Sponsor income..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                        <i class="fa-solid fa-save"></i> Save Income
                    </button>
                </form>
            </div>

            {{-- Weekly Income Table --}}
            <div class="card">
                <div class="card-header" style="margin-bottom:15px;">
                    <div class="card-title"><i class="fa-solid fa-list" style="color:var(--accent)"></i> Weekly Income History</div>
                </div>

                @if($incomes->isEmpty())
                    <div class="empty-state" style="padding:40px 20px;">
                        <i class="fa-solid fa-money-bill-trend-up"></i>
                        <p>No weekly income recorded yet.</p>
                    </div>
                @else
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>Week Period</th>
                                    <th>Notes</th>
                                    <th>Amount</th>
                                    <th style="width: 80px; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incomes as $income)
                                    <tr>
                                        <td>
                                            <strong style="color: var(--text-primary);">{{ $income->period_label }}</strong>
                                        </td>
                                        <td>{{ $income->notes ?? '—' }}</td>
                                        <td class="amount-display income-amount">₱{{ number_format($income->amount, 2) }}</td>
                                        <td style="text-align: center;">
                                            <form method="POST" action="{{ route('financials.destroy_income', $income) }}" onsubmit="return confirm('Are you sure you want to delete this weekly income record?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination">
                        {{ $incomes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab: Outstanding Balances ────────────────────────────── --}}
    <div id="tab-balances" class="tab-panel">
        <div class="card">
            <div class="card-header" style="margin-bottom:15px; display:flex; flex-direction:column; align-items:stretch; gap:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div class="card-title">
                        <i class="fa-solid fa-scale-unbalanced" style="color:var(--accent)"></i> Rider Outstanding Balances
                    </div>
                </div>
                
                {{-- Search & Filter form --}}
                <form method="GET" action="{{ route('financials.index') }}" class="filter-bar" style="margin-bottom:0;">
                    <input type="hidden" name="active_tab" value="balances">
                    <div class="form-group">
                        <label>Rider Search</label>
                        <input type="text" name="rider_search" placeholder="Name or employee ID…" value="{{ request('rider_search') }}">
                    </div>
                    <div class="form-group">
                        <label>Filter</label>
                        <select name="filter_balance">
                            <option value="has_balance" {{ request('filter_balance', 'has_balance') === 'has_balance' ? 'selected' : '' }}>Only with Outstanding Balance</option>
                            <option value="all" {{ request('filter_balance') === 'all' ? 'selected' : '' }}>All Riders</option>
                        </select>
                    </div>
                    <div class="form-group" style="display:flex; gap:8px; align-items:flex-end;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-magnifying-glass"></i> Search
                        </button>
                        <a href="{{ route('financials.index', ['active_tab' => 'balances']) }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            @if($outstandingRiders->isEmpty())
                <div class="empty-state" style="padding:40px 20px;">
                    <i class="fa-solid fa-user-check"></i>
                    <p>No riders with outstanding balances found.</p>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Rider</th>
                                <th>Employee ID</th>
                                <th>Carried Balance (Payslips)</th>
                                <th>Pending Cash Advances</th>
                                <th>Total Outstanding</th>
                                <th style="width: 200px; text-align: center;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($outstandingRiders as $row)
                                @php
                                    $pendingCaSum = $row->pendingCashAdvances->sum('amount');
                                    $totalOutstanding = $row->carried_balance + $pendingCaSum;
                                @endphp
                                <tr>
                                    <td>
                                        <a href="{{ route('riders.show', $row) }}" style="color:var(--text-primary); text-decoration:none;">
                                            <strong>{{ $row->name }}</strong>
                                        </a>
                                    </td>
                                    <td style="color:var(--text-muted); font-size:12px; font-family:monospace;">
                                        {{ $row->employee_id ?? '—' }}
                                    </td>
                                    <td>
                                        @if($row->carried_balance > 0)
                                            <span style="color:var(--danger); font-weight:600;">₱{{ number_format($row->carried_balance, 2) }}</span>
                                        @else
                                            <span style="color:var(--text-muted);">₱0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($pendingCaSum > 0)
                                            <span style="color:var(--warning); font-weight:600;">₱{{ number_format($pendingCaSum, 2) }}</span>
                                            <div style="font-size:11px; color:var(--text-muted);">{{ $row->pendingCashAdvances->count() }} pending</div>
                                        @else
                                            <span style="color:var(--text-muted);">₱0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($totalOutstanding > 0)
                                            <span class="badge badge-danger" style="font-size:13px; padding:6px 12px;">₱{{ number_format($totalOutstanding, 2) }}</span>
                                        @else
                                            <span class="badge badge-success" style="font-size:13px; padding:6px 12px;">₱0.00</span>
                                        @endif
                                    </td>
                                    <td style="text-align: center;">
                                        <div style="display:flex; gap:6px; justify-content:center; align-items:center;">
                                            <a href="{{ route('riders.show', $row) }}" class="btn btn-secondary btn-sm" title="View Profile">
                                                <i class="fa-solid fa-eye"></i> Profile
                                            </a>
                                            <a href="{{ route('payslips.create', ['rider_id' => $row->id]) }}" class="btn btn-primary btn-sm" title="Create Payslip">
                                                <i class="fa-solid fa-file-invoice-dollar"></i> Payslip
                                            </a>
                                            @if($row->carried_balance > 0)
                                                <form method="POST" action="{{ route('financials.clear_balance', $row) }}" onsubmit="return confirm('Clear the carried outstanding balance for {{ $row->name }}?')" style="display:inline; margin:0;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Clear Balance">
                                                        <i class="fa-solid fa-eraser"></i> Clear Bal
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="pagination">
                    {{ $outstandingRiders->links() }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // ── Tab switching ──────────────────────────────────────────────────
            function switchTab(name) {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                
                if(name === 'expenses') {
                    document.getElementById('btn-expenses').classList.add('active');
                    document.getElementById('tab-expenses').classList.add('active');
                    localStorage.setItem('active_finance_tab', 'expenses');
                } else if(name === 'income') {
                    document.getElementById('btn-income').classList.add('active');
                    document.getElementById('tab-income').classList.add('active');
                    localStorage.setItem('active_finance_tab', 'income');
                } else if(name === 'balances') {
                    document.getElementById('btn-balances').classList.add('active');
                    document.getElementById('tab-balances').classList.add('active');
                    localStorage.setItem('active_finance_tab', 'balances');
                }
            }

            // Restore active tab on load
            document.addEventListener('DOMContentLoaded', function() {
                const activeTab = localStorage.getItem('active_finance_tab') || 'expenses';
                switchTab(activeTab);

                // If pagination/tab parameter is in the URL, switch to that tab
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('incomes_page')) {
                    switchTab('income');
                } else if (urlParams.has('expenses_page')) {
                    switchTab('expenses');
                } else if (urlParams.has('riders_page') || urlParams.has('rider_search') || urlParams.get('active_tab') === 'balances') {
                    switchTab('balances');
                }
            });
        </script>
    @endpush
@endsection
