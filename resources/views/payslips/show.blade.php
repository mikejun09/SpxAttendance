@extends('layouts.app')
@section('title', 'Payslip — ' . $payslip->rider->name)
@section('page-title', 'Payslip Details')
@section('breadcrumb', 'Payslips / ' . $payslip->period_label)

@section('content')
    <div class="page-header">
        <div>
            <h1>{{ $payslip->rider->name }}</h1>
            <p>{{ $payslip->period_label }}</p>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('payslips.print', $payslip) }}" target="_blank" class="btn btn-info">
                <i class="fa-solid fa-print"></i> Print / Save PDF
            </a>
            @if (auth()->user()->isAdmin())
                <form method="POST" action="{{ route('payslips.destroy', $payslip) }}"
                    onsubmit="return confirm('Delete this payslip?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fa-solid fa-trash"></i> Delete</button>
                </form>
            @endif
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 380px; gap:20px; align-items:start;">

        {{-- Attendance breakdown --}}
        <div class="card">
            <div class="card-title" style="margin-bottom:16px;">
                <i class="fa-solid fa-calendar-week" style="color:var(--accent)"></i> Weekly Attendance Breakdown
            </div>

            @if ($attendances->isEmpty())
                <div class="empty-state" style="padding:20px">
                    <p>No attendance records for this week.</p>
                </div>
            @else
                <div class="table-wrap">
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
                                    <td style="color:var(--text-muted); font-size:12px;">{{ $att->date->format('l') }}</td>
                                    <td>
                                        @if ($att->spxAccount)
                                            <span class="badge badge-accent">{{ $att->spxAccount->account_name }}</span>
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

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Pay summary --}}
        <div>
            <div class="card" style="margin-bottom:16px;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
                    <div class="card-title"><i class="fa-solid fa-file-invoice-dollar" style="color:var(--accent)"></i> Pay
                        Summary</div>
                    <span class="badge {{ $payslip->status === 'final' ? 'badge-success' : 'badge-warning' }}">
                        {{ ucfirst($payslip->status) }}
                    </span>
                </div>

                <div style="display:flex; flex-direction:column; gap:1px;">
                    @php
                        $rows = [
                            [
                                'Total Days',
                                number_format($payslip->days_worked + $payslip->half_days * 0.5, 1) . ' days',
                                null,
                            ],
                            ['Daily Rate', '₱' . number_format($payslip->daily_rate, 2), null],
                        ];
                        $hasManual = $payslip->manual_deduction > 0;
                    @endphp

                    @foreach ($rows as [$label, $value, $color])
                        <div
                            style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border); font-size:13px;">
                            <span style="color:var(--text-muted);">{{ $label }}</span>
                            <span
                                style="font-weight:600; {{ $color ? 'color:' . $color : '' }}">{{ $value }}</span>
                        </div>
                    @endforeach

                    <div
                        style="display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px solid var(--border);">
                        <span style="font-weight:600;">Gross Pay</span>
                        <span
                            style="font-weight:700; font-size:18px; color:var(--accent);">₱{{ number_format($payslip->gross_pay, 2) }}</span>
                    </div>

                    @if ($payslip->cash_advance_deduction > 0)
                        <div
                            style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border); font-size:13px;">
                            <span style="color:var(--text-muted);">Cash Advance Deduction</span>
                            <span style="font-weight:600; color:var(--danger);">–
                                ₱{{ number_format($payslip->cash_advance_deduction, 2) }}</span>
                        </div>
                    @endif

                    @if ($hasManual)
                        <div
                            style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border); font-size:13px;">
                            <span style="color:var(--text-muted);">Manual Deductions</span>
                            <span style="font-weight:600; color:var(--danger);">–
                                ₱{{ number_format($payslip->manual_deduction, 2) }}</span>
                        </div>
                        @foreach ($payslip->deductions as $ded)
                            <div
                                style="display:flex; justify-content:space-between; padding:4px 0 4px 16px; border-bottom:1px solid var(--border); font-size:12px;">
                                <span style="color:var(--text-muted);">↳ {{ $ded->label }}</span>
                                <span style="color:var(--danger);">₱{{ number_format($ded->amount, 2) }}</span>
                            </div>
                        @endforeach
                    @endif

                    <div style="display:flex; justify-content:space-between; padding:16px 0; margin-top:4px;">
                        <span style="font-weight:700; font-size:16px;">NET PAY</span>
                        <span
                            style="font-weight:800; font-size:24px; color:var(--success);">₱{{ number_format($payslip->net_pay, 2) }}</span>
                    </div>
                </div>
            </div>

            @if ($payslip->cashAdvances->isNotEmpty())
                <div class="card" style="margin-bottom:16px;">
                    <div class="card-title" style="margin-bottom:12px;">
                        <i class="fa-solid fa-money-bill-wave" style="color:var(--warning)"></i> Deducted Cash Advances
                    </div>
                    @foreach ($payslip->cashAdvances as $ca)
                        <div
                            style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--border); font-size:13px;">
                            <span style="color:var(--text-muted);">{{ $ca->date->format('M d') }}
                                {{ $ca->notes ? '— ' . $ca->notes : '' }}</span>
                            <span
                                style="font-weight:600; color:var(--warning);">₱{{ number_format($ca->amount, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($payslip->notes)
                <div class="card">
                    <div class="card-title" style="margin-bottom:8px;"><i class="fa-solid fa-note-sticky"
                            style="color:var(--accent)"></i> Notes</div>
                    <p style="font-size:13px; color:var(--text-muted);">{{ $payslip->notes }}</p>
                </div>
            @endif
        </div>
    </div>
@endsection
