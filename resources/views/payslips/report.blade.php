<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Report — {{ $weekStart->format('M d') }}–{{ $weekEnd->format('M d, Y') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f0f2f5;
            color: #1e293b;
            font-size: 12px;
        }

        /* ── Screen-only action bar ─────────────────────── */
        .action-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1a1a2e;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .4);
        }
 
        .action-bar form {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .action-bar label {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 500;
        }

        .action-bar select,
        .action-bar input[type="date"] {
            background: #0f1117;
            border: 1px solid #2a3450;
            color: #f1f5f9;
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 12px;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
        }

        .ab-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 16px;
            border-radius: 7px;
            border: none;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            text-decoration: none;
        }

        .ab-btn-primary {
            background: #f97316;
            color: #fff;
        }

        .ab-btn-secondary {
            background: #2a3450;
            color: #94a3b8;
        }

        .ab-btn:hover {
            opacity: .88;
        }

        .ab-week {
            margin-left: auto;
            font-size: 11px;
            color: #64748b;
            font-weight: 500;
        }

        /* ── Page ───────────────────────────────────────── */
        .page-wrap {
            margin-top: 58px;
            /* offset action bar */
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .page {
            width: 270mm;
            /* landscape A4 ≈ 297mm minus margins */
            background: #fff;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .12);
            border-radius: 6px;
            overflow: hidden;
        }

        /* ── Report header ───────────────────────────────── */
        .rpt-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            padding: 14px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .rpt-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .rpt-logo-icon {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, #f97316, #c2510a);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .rpt-company {
            font-size: 15px;
            font-weight: 800;
        }

        .rpt-company span {
            color: #f97316;
        }

        .rpt-sub {
            font-size: 9px;
            color: #94a3b8;
            letter-spacing: .8px;
            text-transform: uppercase;
        }

        .rpt-title-block {
            text-align: right;
        }

        .rpt-title {
            font-size: 14px;
            font-weight: 700;
            color: #f97316;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .rpt-period {
            font-size: 11px;
            color: #94a3b8;
            margin-top: 2px;
        }

        .rpt-generated {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }

        .accent-bar {
            height: 3px;
            background: linear-gradient(90deg, #f97316, #fdba74);
        }


        /* ── Main table ───────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead tr {
            background: #f8fafc;
        }

        th {
            padding: 7px 8px;
            font-size: 8.5px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            border-right: 1px solid #f1f5f9;
            text-align: left;
            white-space: nowrap;
        }

        th:last-child {
            border-right: none;
        }

        th.right {
            text-align: right;
        }

        td {
            padding: 5px 8px;
            font-size: 10.5px;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f8fafc;
            vertical-align: middle;
        }

        td:last-child {
            border-right: none;
        }

        td.right {
            text-align: right;
        }

        tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        tbody tr:hover {
            background: #f0f9ff;
        }

        .td-name {
            font-weight: 600;
            font-size: 11px;
        }

        .td-empid {
            font-size: 8.5px;
            color: #94a3b8;
        }

        .td-status {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 8.5px;
            font-weight: 600;
        }

        .s-final {
            background: #dcfce7;
            color: #15803d;
        }

        .s-draft {
            background: #fef3c7;
            color: #d97706;
        }

        .red {
            color: #dc2626;
            font-weight: 600;
        }

        .green {
            color: #15803d;
            font-weight: 700;
        }

        .muted {
            color: #94a3b8;
        }

        .deductions-detail {
            font-size: 8px;
            color: #94a3b8;
            margin-top: 1px;
        }

        /* ── Totals row ────────────────────────────────────── */
        .totals-row td {
            background: #1a1a2e;
            color: #f1f5f9;
            font-weight: 700;
            font-size: 11px;
            padding: 7px 8px;
            border-top: 2px solid #f97316;
            border-bottom: none;
        }

        .totals-row td.right {
            text-align: right;
        }

        .totals-row .grand-net {
            color: #4ade80;
            font-size: 13px;
            font-weight: 800;
        }

        /* ── Footer ────────────────────────────────────────── */
        .rpt-footer {
            padding: 10px 22px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-top: 1px solid #e2e8f0;
            margin-top: 4px;
        }

        .sig-block {
            flex: 1;
        }

        .sig-line {
            border-top: 1px solid #334155;
            margin-top: 28px;
            margin-bottom: 3px;
        }

        .sig-lbl {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .generated-note {
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
        }

        /* ── Empty state ────────────────────────────────────── */
        .empty-note {
            padding: 30px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
        }

        /* ── PRINT ──────────────────────────────────────────── */
        @media print {
            body {
                background: #fff;
                font-size: 10px;
            }

            .action-bar {
                display: none !important;
            }

            .page-wrap {
                margin-top: 0;
                padding: 0;
            }

            .page {
                width: 100%;
                box-shadow: none;
                border-radius: 0;
            }

            @page {
                size: A4 landscape;
                margin: 8mm 10mm;
            }

            tbody tr:hover {
                background: transparent;
            }
        }
    </style>
</head>

<body>

    {{-- ── Screen action bar ──────────────────────────────────────── --}}
    <div class="action-bar">
        <form method="GET" action="{{ route('payslips.report') }}" style="display:flex;align-items:center;gap:16px;">
            <div style="display:flex;align-items:center;gap:8px;">
                <label>Pay Week History:</label>
                <select name="week_start" onchange="document.getElementById('custom_week_start').value = ''; this.form.submit()">
                    <option value="">— Select week —</option>
                    @foreach ($weeks as $w)
                        @php $ws = \Carbon\Carbon::parse($w->week_start); @endphp
                        <option value="{{ $w->week_start }}"
                            {{ $weekStart->toDateString() === $w->week_start ? 'selected' : '' }}>
                            {{ $ws->format('M d') }} –
                            {{ $ws->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('M d, Y') }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div style="display:flex;align-items:center;gap:8px;">
                <label>Or Custom Start Date (Mon):</label>
                <input type="date" name="custom_week_start" id="custom_week_start" value="{{ $weekStart->toDateString() }}" 
                       onchange="document.querySelector('select[name=week_start]').value = ''; this.form.submit()" 
                       onclick="this.showPicker()" style="cursor: pointer;">
            </div>
        </form>

        <button onclick="window.print()" class="ab-btn ab-btn-primary">🖨 Print / Save PDF</button>
        <a href="{{ route('payslips.index') }}" class="ab-btn ab-btn-secondary">← Back</a>

        <span class="ab-week">
            Showing {{ $payslips->count() }} payslip(s) ·
            {{ $weekStart->format('M d') }}–{{ $weekEnd->format('M d, Y') }}
        </span>
    </div>

    {{-- ── Page ────────────────────────────────────────────────────── --}}
    <div class="page-wrap">
        <div class="page">

            {{-- Header --}}
            <div class="rpt-header">
                <div class="rpt-logo">
                    <div class="rpt-logo-icon">🚴</div>
                    <div>
                        <div class="rpt-company"><span>SPX</span> Riders</div>
                        <div class="rpt-sub">Attendance Management System</div>
                    </div>
                </div>
                <div class="rpt-title-block">
                    <div class="rpt-title">Weekly Payroll Report</div>
                    <div class="rpt-period">{{ $weekStart->format('F d') }} – {{ $weekEnd->format('F d, Y') }}</div>
                    <div class="rpt-generated">Generated: {{ now()->format('M d, Y h:i A') }}</div>
                </div>
            </div>
            <div class="accent-bar"></div>

            @if ($payslips->isEmpty())
                <div class="empty-note">No payslips found for this pay period.</div>
            @else
                {{-- Main table --}}
                <table>
                    <thead>
                        <tr>
                            <th style="width:20px;">#</th>
                            <th style="min-width:140px;">Rider</th>
                            <th class="right">Days Worked</th>
                            <th class="right">Daily Rate</th>
                            <th class="right">Gross Pay</th>
                            <th class="right">Add. Pay</th>
                            <th class="right">CA Deduction</th>
                            <th class="right">Other Deductions</th>
                            <th class="right">Net Pay</th>
                            <th>Status</th>
                            <th style="min-width:120px;">Signature</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payslips as $i => $ps)
                            <tr>
                                <td class="muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="td-name">{{ $ps->rider->name }}</div>
                                    @if ($ps->rider->employee_id)
                                        <div class="td-empid">{{ $ps->rider->employee_id }}</div>
                                    @endif
                                </td>
                                <td class="right">
                                    {{ $ps->days_worked }}{{ $ps->half_days ? ' + ' . $ps->half_days . '×½' : '' }}
                                </td>
                                <td class="right muted">₱{{ number_format($ps->daily_rate, 2) }}</td>
                                <td class="right">₱{{ number_format($ps->gross_pay, 2) }}</td>
                                <td class="right">
                                    @if ($ps->additional_pay > 0)
                                        <span class="green">₱{{ number_format($ps->additional_pay, 2) }}</span>
                                        @if ($ps->additions->isNotEmpty())
                                            <div class="deductions-detail" style="color:#16a34a;">
                                                @foreach ($ps->additions as $a)
                                                    {{ $a->label }}@if (!$loop->last), @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td class="right">
                                    @if ($ps->cash_advance_deduction > 0)
                                        <span class="red">₱{{ number_format($ps->cash_advance_deduction, 2) }}</span>
                                        @if ($ps->cashAdvances->isNotEmpty())
                                            <div class="deductions-detail">{{ $ps->cashAdvances->count() }} advance(s)</div>
                                        @endif
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td class="right">
                                    @if ($ps->manual_deduction > 0)
                                        <span class="red">₱{{ number_format($ps->manual_deduction, 2) }}</span>
                                        @if ($ps->deductions->isNotEmpty())
                                            <div class="deductions-detail">
                                                @foreach ($ps->deductions as $d)
                                                    {{ $d->label }}@if (!$loop->last), @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td class="right">
                                    <span class="green">₱{{ number_format($ps->net_pay, 2) }}</span>
                                </td>
                                <td>
                                    <span class="td-status {{ $ps->status === 'final' ? 's-final' : 's-draft' }}">
                                        {{ ucfirst($ps->status) }}
                                    </span>
                                </td>
                                <td style="border-bottom: 1px solid #e2e8f0;"></td>
                            </tr>
                        @endforeach
                    </tbody>
                    {{-- Totals row --}}
                    <tr class="totals-row">
                        <td colspan="2">TOTAL — {{ $payslips->count() }} Riders</td>
                        <td class="right">{{ $totals['days'] }}</td>
                        <td class="right">—</td>
                        <td class="right">₱{{ number_format($totals['gross'], 2) }}</td>
                        <td class="right" style="color:#86efac;">₱{{ number_format($totals['addition'], 2) }}</td>
                        <td class="right" style="color:#fca5a5;">₱{{ number_format($totals['ca'], 2) }}</td>
                        <td class="right" style="color:#fca5a5;">₱{{ number_format($totals['manual'], 2) }}</td>
                        <td class="right grand-net">₱{{ number_format($totals['net'], 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </table>

                {{-- Footer / signatures --}}
                <div class="rpt-footer">
                    <div class="sig-block">
                        <div class="sig-line"></div>
                        <div class="sig-lbl">Prepared by</div>
                    </div>
                    <div style="width:24px;"></div>
                    <div class="sig-block">
                        <div class="sig-line"></div>
                        <div class="sig-lbl">Checked by</div>
                    </div>
                    <div style="width:24px;"></div>
                    <div class="sig-block">
                        <div class="sig-line"></div>
                        <div class="sig-lbl">Approved by</div>
                    </div>
                    <div style="width:32px;"></div>
                    <div class="generated-note">
                        SPX Rider Attendance System<br>
                        Generated {{ now()->format('F d, Y h:i A') }}
                    </div>
                </div>

            @endif

        </div>{{-- .page --}}
    </div>{{-- .page-wrap --}}

</body>

</html>
