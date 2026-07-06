<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip — {{ $payslip->rider->name }} — {{ $payslip->period_label }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', Arial, sans-serif;
            background: #f5f5f5;
            color: #1a1a2e;
            font-size: 14px;
        }

        .print-actions {
            position: fixed;
            top: 16px;
            right: 16px;
            display: flex;
            gap: 10px;
            z-index: 100;
        }

        .print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            font-family: 'Inter', sans-serif;
        }

        .btn-print {
            background: #f97316;
            color: #fff;
        }

        .btn-back {
            background: #e2e8f0;
            color: #334155;
        }

        .page {
            width: 210mm;
            min-height: 148mm;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 4px 24px rgba(0, 0, 0, .15);
            border-radius: 8px;
            overflow: hidden;
        }

        /* Header */
        .slip-header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            padding: 28px 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company-logo {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-icon {
            width: 52px;
            height: 52px;
            background: linear-gradient(135deg, #f97316, #c2510a);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .company-name {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .company-name span {
            color: #f97316;
        }

        .company-sub {
            font-size: 11px;
            color: #94a3b8;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .slip-title {
            text-align: right;
        }

        .slip-title h2 {
            font-size: 18px;
            font-weight: 700;
            color: #f97316;
        }

        .slip-title .period {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        .slip-title .slip-no {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
            font-family: monospace;
        }

        /* Orange accent bar */
        .accent-bar {
            height: 4px;
            background: linear-gradient(90deg, #f97316, #fdba74);
        }

        /* Body */
        .slip-body {
            padding: 24px 32px;
        }

        /* Rider info */
        .rider-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .rider-info .name {
            font-size: 18px;
            font-weight: 700;
        }

        .rider-info .meta {
            font-size: 12px;
            color: #64748b;
            margin-top: 4px;
        }

        .rider-badge {
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #c2410c;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Attendance table */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th {
            background: #f8fafc;
            padding: 8px 12px;
            text-align: left;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: .6px;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        td {
            padding: 9px 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            color: #1e293b;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .s-present {
            background: #dcfce7;
            color: #15803d;
        }

        .s-absent {
            background: #fee2e2;
            color: #dc2626;
        }

        .s-half {
            background: #fef3c7;
            color: #d97706;
        }

        .s-rest {
            background: #f1f5f9;
            color: #64748b;
        }

        /* Pay summary */
        .pay-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .pay-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px 16px;
        }

        .pay-box .label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 4px;
        }

        .pay-box .value {
            font-size: 16px;
            font-weight: 700;
        }

        .pay-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
        }

        .pay-row:last-child {
            border-bottom: none;
        }

        .pay-row .lbl {
            color: #64748b;
        }

        .net-pay-box {
            background: linear-gradient(135deg, #f97316, #c2510a);
            border-radius: 10px;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            margin-top: 16px;
        }

        .net-pay-box .label {
            font-size: 13px;
            font-weight: 600;
            opacity: .85;
        }

        .net-pay-box .amount {
            font-size: 32px;
            font-weight: 800;
        }

        /* Footer */
        .slip-footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 24px;
            padding-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 20px;
        }

        .sig-line {
            flex: 1;
        }

        .sig-line .line {
            border-top: 1.5px solid #1e293b;
            margin-bottom: 6px;
        }

        .sig-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .generated-at {
            font-size: 10px;
            color: #94a3b8;
            text-align: right;
        }

        /* Print styles */
        @media print {
            body {
                background: #fff;
            }

            .print-actions {
                display: none !important;
            }

            .page {
                box-shadow: none;
                margin: 0;
                border-radius: 0;
                width: 100%;
            }

            @page {
                margin: 10mm;
                size: A4;
            }
        }
    </style>
</head>

<body>

    <div class="print-actions">
        <button onclick="window.print()" class="print-btn">🖨 Print / Save PDF</button>
        <a href="{{ route('payslips.show', $payslip) }}" class="print-btn btn-back">← Back</a>
    </div>

    <div class="page">
        {{-- Header --}}
        <div class="slip-header">
            <div class="company-logo">
                <div class="logo-icon">🚴</div>
                <div>
                    <div class="company-name"><span>SPX</span> Riders</div>
                    <div class="company-sub">Attendance Management System</div>
                </div>
            </div>
            <div class="slip-title">
                <h2>PAYSLIP</h2>
                <div class="period">{{ $payslip->week_start->format('M d') }} –
                    {{ $payslip->week_end->format('M d, Y') }}</div>
                <div class="slip-no">REF #{{ str_pad($payslip->id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>
        </div>
        <div class="accent-bar"></div>

        {{-- Body --}}
        <div class="slip-body">

            {{-- Rider Info --}}
            <div class="rider-section">
                <div class="rider-info">
                    <div class="name">{{ $payslip->rider->name }}</div>
                    <div class="meta">
                        @if ($payslip->rider->employee_id)
                            Employee ID: {{ $payslip->rider->employee_id }} &bull;
                        @endif
                        @if ($payslip->rider->contact_number)
                            {{ $payslip->rider->contact_number }}
                        @endif
                    </div>
                </div>
                <span class="rider-badge">{{ ucfirst($payslip->status) }}</span>
            </div>

            {{-- Attendance --}}
            <div class="section-title">Weekly Attendance</div>
            @if ($attendances->isEmpty())
                <p style="color:#94a3b8; font-size:13px; margin-bottom:20px;">No attendance records for this week.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                            <th>SPX Account</th>
                            <th>Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($attendances as $att)
                            <tr>
                                <td>{{ $att->date->format('M d, Y') }}</td>
                                <td style="color:#64748b;">{{ $att->date->format('l') }}</td>
                                <td style="color:#64748b; font-size:12px;">{{ $att->spxAccount?->account_name ?? '—' }}
                                </td>
                                <td>
                                    @php
                                        $sc = match ($att->status) {
                                            'present' => 's-present',
                                            'absent' => 's-absent',
                                            'half_day' => 's-half',
                                            default => 's-rest',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $sc }}">{{ $att->status_label }}</span>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            {{-- Pay Breakdown --}}
            <div class="section-title">Pay Computation</div>

            <div class="pay-grid">
                <div class="pay-box">
                    <div class="label">Full Days Present</div>
                    <div class="value">{{ $payslip->days_worked }} days</div>
                </div>


                <div class="pay-box">
                    <div class="label">Daily Rate</div>
                    <div class="value">₱{{ number_format($payslip->daily_rate, 2) }}</div>
                </div>
            </div>

            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px;">
                <div class="pay-row">
                    <span class="lbl">Gross Pay</span>
                    <span style="font-weight:700; font-size:15px;">₱{{ number_format($payslip->gross_pay, 2) }}</span>
                </div>
                @if ($payslip->cash_advance_deduction > 0)
                    <div class="pay-row">
                        <span class="lbl">Cash Advance Deduction</span>
                        <span style="font-weight:600; color:#dc2626;">–
                            ₱{{ number_format($payslip->cash_advance_deduction, 2) }}</span>
                    </div>
                    @if ($payslip->cashAdvances->isNotEmpty())
                        @foreach ($payslip->cashAdvances as $ca)
                            <div class="pay-row" style="padding:4px 0 4px 16px;">
                                <span class="lbl" style="font-size:12px;">↳
                                    {{ $ca->date->format('M d') }}{{ $ca->notes ? ' — ' . $ca->notes : '' }}</span>
                                <span
                                    style="font-size:12px; color:#dc2626;">₱{{ number_format($ca->amount, 2) }}</span>
                            </div>
                        @endforeach
                    @endif
                @endif
                @if ($payslip->manual_deduction > 0)
                    <div class="pay-row">
                        <span class="lbl">Manual Deductions</span>
                        <span style="font-weight:600; color:#dc2626;">–
                            ₱{{ number_format($payslip->manual_deduction, 2) }}</span>
                    </div>
                    @foreach ($payslip->deductions as $ded)
                        <div class="pay-row" style="padding:4px 0 4px 16px;">
                            <span class="lbl" style="font-size:12px;">↳ {{ $ded->label }}</span>
                            <span style="font-size:12px; color:#dc2626;">₱{{ number_format($ded->amount, 2) }}</span>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="net-pay-box">
                <div class="label">NET PAY FOR THE WEEK</div>
                <div class="amount">₱{{ number_format($payslip->net_pay, 2) }}</div>
            </div>

            @if ($payslip->notes)
                <div
                    style="margin-top:16px; padding:12px 16px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px;">
                    <div
                        style="font-size:11px; font-weight:700; color:#92400e; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">
                        Notes</div>
                    <div style="font-size:13px; color:#78350f;">{{ $payslip->notes }}</div>
                </div>
            @endif

            {{-- Signature --}}
            <div class="slip-footer">
                <div class="sig-line">
                    <div class="line" style="margin-top:40px;"></div>
                    <div class="sig-label">Rider's Signature</div>
                </div>
                <div class="sig-line">
                    <div class="line" style="margin-top:40px;"></div>
                    <div class="sig-label">Prepared by</div>
                </div>
                <div class="sig-line">
                    <div class="line" style="margin-top:40px;"></div>
                    <div class="sig-label">Approved by</div>
                </div>
            </div>

            <div class="generated-at" style="margin-top:12px;">
                Generated on {{ $payslip->created_at->format('F d, Y h:i A') }} &bull; SPX Rider Attendance System
            </div>

        </div>
    </div>

    <script>
        // Auto-print on load if ?print=1
        if (new URLSearchParams(window.location.search).get('print') === '1') {
            window.addEventListener('load', () => window.print());
        }
    </script>
</body>

</html>
