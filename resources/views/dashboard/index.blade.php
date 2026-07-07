@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('breadcrumb', 'Welcome back, ' . auth()->user()->name)

@push('styles')
    <style>
        /* ── Rider management panel ──────────────────────────────────────── */
        .rider-panel-row {
            display: grid;
            grid-template-columns: 1fr 220px 160px auto;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            transition: background .15s;
        }

        .rider-panel-row:last-child {
            border-bottom: none;
        }

        .rider-panel-row:hover {
            background: var(--bg-hover);
        }

        .rider-avatar-sm {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }

        .rider-info-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .inline-spx-form {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .inline-spx-form select {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 6px;
            min-width: 0;
            flex: 1;
        }

        .inline-spx-form .save-btn {
            padding: 6px 10px;
            font-size: 12px;
            border-radius: 6px;
            background: var(--accent);
            color: #fff;
            border: none;
            cursor: pointer;
            white-space: nowrap;
            font-weight: 600;
            transition: all .2s;
        }

        .inline-spx-form .save-btn:hover {
            background: var(--accent-dark);
        }

        .action-cell {
            display: flex;
            gap: 6px;
        }

        /* Payslip quick form */
        .payslip-quick {
            background: var(--bg-hover);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }

        .payslip-quick .form-group {
            margin: 0;
            flex: 1;
            min-width: 160px;
        }

        .payslip-quick label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 5px;
        }

        .payslip-quick select,
        .payslip-quick input[type="date"] {
            padding: 8px 10px;
            font-size: 13px;
        }

        .panel-header-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 16px;
        }

        .tab-btn {
            padding: 7px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-muted);
            transition: all .2s;
            font-family: inherit;
        }

        .tab-btn.active {
            background: rgba(249, 115, 22, .15);
            color: var(--accent);
            border-color: rgba(249, 115, 22, .3);
        }

        .tab-panel {
            display: none;
        }

        .tab-panel.active {
            display: block;
        }

        /* ── Finance dashboard styles ───────────────────────────────────── */
        .finance-toggle-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .finance-toggle-btns {
            display: flex;
            background: var(--bg-secondary);
            border: 1px solid var(--border);
            padding: 4px;
            border-radius: 8px;
        }

        .finance-toggle-btn {
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            transition: all .2s;
        }

        .finance-toggle-btn.active {
            background: var(--accent);
            color: #fff;
        }

        .finance-summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .finance-summary-card {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .finance-summary-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--text-secondary);
        }

        .finance-summary-value {
            font-size: 22px;
            font-weight: 700;
        }

        .finance-breakdown-box {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 16px;
        }

        .finance-breakdown-title {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: var(--accent);
            margin-bottom: 12px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .finance-breakdown-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 8px 0;
            border-bottom: 1px dashed var(--border);
            color: var(--text-secondary);
        }

        .finance-breakdown-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .finance-breakdown-val {
            font-weight: 600;
            color: var(--text-primary);
        }

        /* Month picker styling to match date picker */
        .date-picker input[type="month"] {
            width: auto;
            padding: 9px 14px 9px 40px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s, background .2s;
            appearance: none;
            -webkit-appearance: none;
            min-width: 160px;
        }
        .date-picker input[type="month"]:hover {
            border-color: var(--accent);
            background: var(--bg-hover);
        }
        .date-picker input[type="month"]:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, .18);
            background: var(--bg-hover);
        }
        .date-picker input[type="month"]::-webkit-calendar-picker-indicator {
            position: absolute;
            right: 0;
            left: 0;
            top: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')

    {{-- ── Financial Performance Overview ────────────────────────────── --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="finance-toggle-container">
            <div class="card-title">
                <i class="fa-solid fa-wallet" style="color:var(--accent)"></i> Financial Performance Overview
            </div>
            <div class="finance-toggle-btns">
                <button id="btn-period-week" class="finance-toggle-btn {{ $activePeriod === 'week' ? 'active' : '' }}" onclick="toggleFinancePeriod('week')">This Week</button>
                <button id="btn-period-month" class="finance-toggle-btn {{ $activePeriod === 'month' ? 'active' : '' }}" onclick="toggleFinancePeriod('month')">This Month</button>
            </div>
        </div>

        {{-- Week Panel --}}
        <div id="finance-week" class="finance-period-panel" style="{{ $activePeriod === 'week' ? '' : 'display:none;' }}">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; gap: 12px; flex-wrap: wrap; background: var(--bg-hover); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 13px; color: var(--text-secondary);">
                    Week of <strong style="color:var(--text-primary);">{{ \Carbon\Carbon::parse($weekStart)->format('M d, Y') }}</strong> to <strong style="color:var(--text-primary);">{{ \Carbon\Carbon::parse($weekEnd)->format('M d, Y') }}</strong>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 8px;">
                    <input type="hidden" name="period" value="week">
                    @if(request('month'))
                        <input type="hidden" name="month" value="{{ request('month') }}">
                    @endif
                    <label for="week_date" style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin: 0;">Select Date:</label>
                    <div class="date-picker">
                        <i class="fa-solid fa-calendar-week dp-icon"></i>
                        <input type="date" name="week_date" id="week_date" value="{{ $selectedWeekDate }}" onchange="this.form.submit()">
                    </div>
                </form>
            </div>
            <div class="finance-summary-grid">
                <div class="finance-summary-card">
                    <span class="finance-summary-label">Total Weekly Income</span>
                    <span class="finance-summary-value" style="color:var(--success)">₱{{ number_format($weeklyFinancials['income'], 2) }}</span>
                </div>
                <div class="finance-summary-card">
                    <span class="finance-summary-label">Total Weekly Expenses</span>
                    <span class="finance-summary-value" style="color:var(--danger)">₱{{ number_format($weeklyFinancials['total_expenses'], 2) }}</span>
                </div>
                <div class="finance-summary-card">
                    <span class="finance-summary-label">Net Profit / Loss</span>
                    <span class="finance-summary-value" style="color: {{ $weeklyFinancials['net_profit'] >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                        ₱{{ number_format($weeklyFinancials['net_profit'], 2) }}
                    </span>
                </div>
            </div>

            <div class="finance-breakdown-box">
                <div class="finance-breakdown-title">
                    <span>Expense Breakdown (Weekly)</span>
                    <span style="font-size:11px; text-transform:none; color:var(--text-muted); font-weight:normal;">Formula: Misc + Cash Advances + Net Salary + Deductions</span>
                </div>
                <div class="finance-breakdown-row">
                    <span>1. Inputted Miscellaneous Expenses</span>
                    <span class="finance-breakdown-val">₱{{ number_format($weeklyFinancials['misc_expenses'], 2) }}</span>
                </div>
                <div class="finance-breakdown-row">
                    <span>2. Cash Advances Released</span>
                    <span class="finance-breakdown-val">₱{{ number_format($weeklyFinancials['cash_advances'], 2) }}</span>
                </div>
                <div class="finance-breakdown-row">
                    <span>3. Rider Salaries (Net Paid)</span>
                    <span class="finance-breakdown-val">₱{{ number_format($weeklyFinancials['net_salary'], 2) }}</span>
                </div>
                <div class="finance-breakdown-row">
                    <span>4. Salary Deductions (Manual)</span>
                    <span class="finance-breakdown-val">₱{{ number_format($weeklyFinancials['deductions'], 2) }}</span>
                </div>
                <div class="finance-breakdown-row" style="border-top:1px solid var(--border); font-weight:700; color:var(--text-primary); padding-top:10px;">
                    <span>Total Calculated Expenses</span>
                    <span style="color:var(--danger)">₱{{ number_format($weeklyFinancials['total_expenses'], 2) }}</span>
                </div>
            </div>
        </div>

        {{-- Month Panel --}}
        <div id="finance-month" class="finance-period-panel" style="{{ $activePeriod === 'month' ? '' : 'display:none;' }}">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; gap: 12px; flex-wrap: wrap; background: var(--bg-hover); padding: 12px 16px; border-radius: 8px; border: 1px solid var(--border);">
                <div style="font-size: 13px; color: var(--text-secondary);">
                    Month of <strong style="color:var(--text-primary);">{{ \Carbon\Carbon::parse($monthStart)->format('F Y') }}</strong>
                </div>
                <form method="GET" action="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 8px;">
                    <input type="hidden" name="period" value="month">
                    @if(request('week_date'))
                        <input type="hidden" name="week_date" value="{{ request('week_date') }}">
                    @endif
                    <label for="month" style="font-size: 12px; font-weight: 600; color: var(--text-secondary); margin: 0;">Select Month:</label>
                    <div class="date-picker">
                        <i class="fa-solid fa-calendar-days dp-icon"></i>
                        <input type="month" name="month" id="month" value="{{ $selectedMonth }}" onchange="this.form.submit()">
                    </div>
                </form>
            </div>
            <div class="finance-summary-grid">
                <div class="finance-summary-card">
                    <span class="finance-summary-label">Total Monthly Income</span>
                    <span class="finance-summary-value" style="color:var(--success)">₱{{ number_format($monthlyFinancials['income'], 2) }}</span>
                </div>
                <div class="finance-summary-card">
                    <span class="finance-summary-label">Total Monthly Expenses</span>
                    <span class="finance-summary-value" style="color:var(--danger)">₱{{ number_format($monthlyFinancials['total_expenses'], 2) }}</span>
                </div>
                <div class="finance-summary-card">
                    <span class="finance-summary-label">Net Profit / Loss</span>
                    <span class="finance-summary-value" style="color: {{ $monthlyFinancials['net_profit'] >= 0 ? 'var(--success)' : 'var(--danger)' }}">
                        ₱{{ number_format($monthlyFinancials['net_profit'], 2) }}
                    </span>
                </div>
            </div>

            <div class="finance-breakdown-box">
                <div class="finance-breakdown-title">
                    <span>Expense Breakdown (Monthly)</span>
                    <span style="font-size:11px; text-transform:none; color:var(--text-muted); font-weight:normal;">Formula: Misc + Cash Advances + Net Salary + Deductions</span>
                </div>
                <div class="finance-breakdown-row">
                    <span>1. Inputted Miscellaneous Expenses</span>
                    <span class="finance-breakdown-val">₱{{ number_format($monthlyFinancials['misc_expenses'], 2) }}</span>
                </div>
                <div class="finance-breakdown-row">
                    <span>2. Cash Advances Released</span>
                    <span class="finance-breakdown-val">₱{{ number_format($monthlyFinancials['cash_advances'], 2) }}</span>
                </div>
                <div class="finance-breakdown-row">
                    <span>3. Rider Salaries (Net Paid)</span>
                    <span class="finance-breakdown-val">₱{{ number_format($monthlyFinancials['net_salary'], 2) }}</span>
                </div>
                <div class="finance-breakdown-row">
                    <span>4. Salary Deductions (Manual)</span>
                    <span class="finance-breakdown-val">₱{{ number_format($monthlyFinancials['deductions'], 2) }}</span>
                </div>
                <div class="finance-breakdown-row" style="border-top:1px solid var(--border); font-weight:700; color:var(--text-primary); padding-top:10px;">
                    <span>Total Calculated Expenses</span>
                    <span style="color:var(--danger)">₱{{ number_format($monthlyFinancials['total_expenses'], 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stats ─────────────────────────────────────────────────────── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon stat-icon-orange"><i class="fa-solid fa-user-group"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total_riders'] }}</div>
                <div class="stat-label">Active Riders</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-green"><i class="fa-solid fa-calendar-check"></i></div>
            <div>
                <div class="stat-value">{{ $stats['present_today'] }}</div>
                <div class="stat-label">Present Today</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-blue"><i class="fa-solid fa-boxes-stacked"></i></div>
            <div>
                <div class="stat-value">{{ $stats['total_spx_accounts'] }}</div>
                <div class="stat-label">SPX Accounts</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-yellow"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div>
                <div class="stat-value">₱{{ number_format($stats['pending_ca'], 0) }}</div>
                <div class="stat-label">Pending CA</div>
            </div>
        </div>

    </div>

    {{-- ── Rider Management Panel ────────────────────────────────────── --}}
    <div class="card" style="margin-bottom:20px;">
        <div class="card-header" style="margin-bottom:0;">
            <div class="card-title">
                <i class="fa-solid fa-user-group" style="color:var(--accent)"></i>
                Rider Management
                <span
                    style="margin-left:8px; background:var(--bg-hover); border:1px solid var(--border); border-radius:20px; padding:2px 10px; font-size:11px; color:var(--text-muted);">
                    {{ $riders->count() }} riders
                </span>
            </div>
            <div style="display:flex; gap:8px;">
                <a href="{{ route('attendance.daily', today()->toDateString()) }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-calendar-day"></i> Mark Attendance
                </a>
                <a href="{{ route('riders.create') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-user-plus"></i> Add Rider
                </a>
            </div>
        </div>

        <div style="margin-top:20px;">
            {{-- Tab buttons --}}
            <div class="panel-header-tabs">
                <button class="tab-btn active" onclick="switchTab('assign')">
                    <i class="fa-solid fa-link"></i> Assign to SPX Account
                </button>
                <button class="tab-btn" onclick="switchTab('payslip')">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Generate Payslip
                </button>
            </div>

            {{-- Tab: Assign to SPX Account ──────────────────────────────── --}}
            <div id="tab-assign" class="tab-panel active">
                @if ($riders->isEmpty())
                    <div class="empty-state" style="padding:30px">
                        <i class="fa-solid fa-user-slash"></i>
                        <p>No active riders. <a href="{{ route('riders.create') }}" style="color:var(--accent)">Add one
                                first</a>.</p>
                    </div>
                @else
                    {{-- Column headers --}}
                    <div
                        style="display:grid; grid-template-columns:1fr 220px 160px auto; gap:12px; padding:8px 16px; font-size:10px; font-weight:700; letter-spacing:.7px; text-transform:uppercase; color:var(--text-muted); border-bottom:2px solid var(--border);">
                        <span>Rider</span>
                        <span>Current SPX Account</span>
                        <span>Daily Rate</span>
                        <span>Actions</span>
                    </div>

                    @foreach ($riders as $rider)
                        <div class="rider-panel-row">
                            {{-- Rider info --}}
                            <div class="rider-info-cell">
                                <div class="rider-avatar-sm">{{ strtoupper(substr($rider->name, 0, 1)) }}</div>
                                <div>
                                    <div style="font-weight:600; font-size:14px;">
                                        <a href="{{ route('riders.show', $rider) }}"
                                            style="color:var(--text-primary); text-decoration:none;">
                                            {{ $rider->name }}
                                        </a>
                                    </div>
                                    <div style="font-size:11px; color:var(--text-muted);">
                                        {{ $rider->employee_id ?? 'No ID' }}
                                        @if ($rider->user_id)
                                            &bull; <span style="color:var(--success);"><i
                                                    class="fa-solid fa-circle-check"></i> Portal</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- SPX inline assignment --}}
                            <div>
                                <form method="POST" action="{{ route('riders.assign-spx', $rider) }}"
                                    class="inline-spx-form">
                                    @csrf
                                    <select name="spx_account_id" title="Select SPX Account">
                                        <option value="">— Unassigned —</option>
                                        @foreach ($spxAccounts as $acct)
                                            <option value="{{ $acct->id }}"
                                                {{ $rider->spx_account_id == $acct->id ? 'selected' : '' }}>
                                                {{ $acct->account_code }} — {{ $acct->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="save-btn" title="Save assignment">
                                        <i class="fa-solid fa-floppy-disk"></i>
                                    </button>
                                </form>
                                @if ($rider->spxAccount)
                                    <div style="font-size:10px; color:var(--accent); margin-top:3px; padding-left:2px;">
                                        <i class="fa-solid fa-link" style="font-size:9px;"></i>
                                        {{ $rider->spxAccount->account_name }}
                                    </div>
                                @endif
                            </div>

                            {{-- Daily rate --}}
                            <div>
                                <span
                                    style="font-size:16px; font-weight:700; color:var(--accent);">₱{{ number_format($rider->daily_rate, 2) }}</span>
                                <div style="font-size:11px; color:var(--text-muted);">per day</div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="action-cell">
                                <a href="{{ route('riders.show', $rider) }}" class="btn btn-secondary btn-sm btn-icon"
                                    title="View Profile">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-primary btn-sm"
                                    onclick="prefillPayslip('{{ $rider->id }}', '{{ addslashes($rider->name) }}')"
                                    title="Generate Payslip">
                                    <i class="fa-solid fa-file-invoice-dollar"></i> Payslip
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Tab: Generate Payslip ─────────────────────────────────────── --}}
            <div id="tab-payslip" class="tab-panel">
                <div
                    style="background:var(--bg-hover); border:1px solid var(--border); border-radius:10px; padding:20px; margin-bottom:16px;">
                    <div style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">
                        <i class="fa-solid fa-circle-info" style="color:var(--accent);"></i>
                        Select a rider and week to preview and generate their weekly payslip.
                        You can also click the <strong style="color:var(--accent);">Payslip</strong> button on any rider row
                        to pre-fill the form.
                    </div>

                    <form method="GET" action="{{ route('payslips.create') }}"
                        style="display:flex; gap:12px; align-items:flex-end; flex-wrap:wrap;">
                        <div class="form-group" style="flex:1; min-width:200px; margin:0;">
                            <label>Select Rider *</label>
                            <select name="rider_id" id="payslipRiderSelect" required>
                                <option value="">— Choose a rider —</option>
                                @foreach ($riders as $rider)
                                    <option value="{{ $rider->id }}" data-name="{{ $rider->name }}">
                                        {{ $rider->name }}
                                        @if ($rider->spxAccount)
                                            ({{ $rider->spxAccount->account_code }})
                                        @endif
                                        — ₱{{ number_format($rider->daily_rate, 0) }}/day
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="min-width:180px; margin:0;">
                            <label>Week Starting (Monday) *</label>
                            <div class="date-picker full">
                                <i class="fa-solid fa-calendar-week dp-icon"></i>
                                <input type="date" name="week_start" id="payslipWeekStart"
                                    value="{{ now()->subWeek()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString() }}"
                                    title="Select the Monday of the pay week" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" style="padding:10px 22px;">
                            <i class="fa-solid fa-calculator"></i> Preview & Generate
                        </button>
                    </form>
                </div>

                {{-- Quick-generate all riders for this week --}}
                <div style="border:1px dashed var(--border); border-radius:10px; padding:16px;">
                    <div style="font-size:13px; font-weight:600; margin-bottom:12px;">
                        <i class="fa-solid fa-bolt" style="color:var(--accent);"></i>
                        Quick-link: Generate payslip for each rider (last week)
                    </div>
                    <div style="display:flex; flex-wrap:wrap; gap:8px;">
                        @php $lastWeekStart = now()->subWeek()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString(); @endphp
                        @foreach ($riders as $rider)
                            <a href="{{ route('payslips.create', ['rider_id' => $rider->id, 'week_start' => $lastWeekStart]) }}"
                                class="btn btn-secondary btn-sm">
                                <i class="fa-solid fa-file-circle-plus"></i>
                                {{ $rider->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Bottom row: Today's Attendance + Recent Payslips ─────────── --}}
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">

        {{-- Today's Attendance --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-clock" style="color:var(--accent)"></i> Today's Attendance
                </div>
                <a href="{{ route('attendance.daily', today()->toDateString()) }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-pen"></i> Mark
                </a>
            </div>

            @if ($todayAttendance->isEmpty())
                <div class="empty-state" style="padding:30px">
                    <i class="fa-solid fa-calendar-day"></i>
                    <p>No attendance recorded yet today.</p>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Rider</th>
                                <th>SPX</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($todayAttendance as $att)
                                <tr>
                                    <td>
                                        <a href="{{ route('riders.show', $att->rider) }}"
                                            style="color:var(--text-primary); text-decoration:none; font-weight:500;">
                                            {{ $att->rider->name }}
                                        </a>
                                    </td>
                                    <td style="color:var(--text-muted); font-size:12px;">
                                        {{ $att->spxAccount?->account_code ?? '—' }}
                                    </td>
                                    <td>
                                        @php
                                            $bc = match ($att->status) {
                                                'present' => 'badge-success',
                                                'absent' => 'badge-danger',
                                                'half_day' => 'badge-warning',
                                                'rest_day' => 'badge-muted',
                                                default => 'badge-info',
                                            };
                                        @endphp
                                        <span class="badge {{ $bc }}">{{ $att->status_label }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Recent Payslips --}}
        <div class="card">
            <div class="card-header">
                <div class="card-title"><i class="fa-solid fa-file-invoice-dollar" style="color:var(--accent)"></i>
                    Recent Payslips</div>
                <a href="{{ route('payslips.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-list"></i> View All
                </a>
            </div>

            @if ($recentPayslips->isEmpty())
                <div class="empty-state" style="padding:30px">
                    <i class="fa-solid fa-file-circle-question"></i>
                    <p>No payslips generated yet.</p>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Rider</th>
                                <th>Period</th>
                                <th>Net Pay</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentPayslips as $ps)
                                <tr>
                                    <td>
                                        <a href="{{ route('payslips.show', $ps) }}"
                                            style="color:var(--text-primary); text-decoration:none; font-weight:500;">
                                            {{ $ps->rider->name }}
                                        </a>
                                    </td>
                                    <td style="font-size:12px; color:var(--text-muted);">{{ $ps->period_label }}</td>
                                    <td><strong
                                            style="color:var(--success);">₱{{ number_format($ps->net_pay, 2) }}</strong>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:5px;">
                                            <a href="{{ route('payslips.show', $ps) }}"
                                                class="btn btn-secondary btn-sm btn-icon" title="View">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a href="{{ route('payslips.print', $ps) }}" target="_blank"
                                                class="btn btn-info btn-sm btn-icon" title="Print">
                                                <i class="fa-solid fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>

    @push('scripts')
        <script>
            // ── Finance period toggling ────────────────────────────────────────
            function toggleFinancePeriod(period) {
                document.querySelectorAll('.finance-period-panel').forEach(p => p.style.display = 'none');
                document.querySelectorAll('.finance-toggle-btn').forEach(b => b.classList.remove('active'));
                
                document.getElementById('finance-' + period).style.display = 'block';
                document.getElementById('btn-period-' + period).classList.add('active');
            }

            // ── Tab switching ──────────────────────────────────────────────────
            function switchTab(name) {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                document.getElementById('tab-' + name).classList.add('active');
                event.currentTarget.classList.add('active');
            }

            // ── Pre-fill payslip tab with a specific rider ─────────────────────
            function prefillPayslip(riderId, riderName) {
                // Switch to the payslip tab
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                document.getElementById('tab-payslip').classList.add('active');
                document.querySelectorAll('.tab-btn')[1].classList.add('active');

                // Set the rider select
                const sel = document.getElementById('payslipRiderSelect');
                if (sel) sel.value = riderId;

                // Scroll to panel smoothly
                document.getElementById('tab-payslip').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        </script>
    @endpush

@endsection
