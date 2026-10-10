<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $job->invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 11px; }
        .top { border-bottom: 2px solid #4f46e5; padding-bottom: 15px; }
        .left { display: inline-block; width: 58%; vertical-align: top; }
        .right { display: inline-block; width: 40%; text-align: right; vertical-align: top; }
        h1 { font-size: 24px; margin: 4px 0; }
        h2 { font-size: 14px; margin: 24px 0 8px; }
        .muted { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { padding: 8px 6px; border-bottom: 1px solid #e2e8f0; text-align: left; vertical-align: top; }
        th { background: #f8fafc; color: #475569; font-size: 9px; text-transform: uppercase; }
        .number { text-align: right; white-space: nowrap; }
        .totals { margin: 16px 0 0 auto; width: 260px; }
        .totals td { border: 0; padding: 6px; }
        .grand { border-top: 1px solid #cbd5e1 !important; font-weight: bold; font-size: 14px; }
        .footer { margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px; color: #64748b; font-size: 9px; }
    </style>
</head>
<body>
    <div class="top">
        <div class="left"><div class="muted">INVOICE</div><h1>{{ $job->invoice_number }}</h1><div>Job #{{ $job->job_number }} · {{ now()->format('F j, Y') }}</div></div>
        <div class="right"><strong>{{ $job->site_business ?: config('app.name') }}</strong><br>Bill to: {{ $job->billing_full_name ?: $job->site_full_name ?: $job->customer?->full_name }}<br>{{ $job->billing_telephone ?: $job->site_telephone }}<br>{{ trim(collect([$job->billing_address ?: $job->site_address, $job->billing_city ?: $job->site_city, $job->billing_state ?: $job->site_state, $job->billing_postal_code ?: $job->site_postal_code])->filter()->implode(', ')) }}</div>
    </div>
    <h2>Work completed</h2>
    <table><thead><tr><th>Area / item</th><th>Description</th><th>Qty</th><th class="number">Amount</th></tr></thead><tbody>
        @foreach ($job->lineItems as $line)<tr><td>{{ $line->area_item ?: '—' }}</td><td>{{ $line->description ?: '—' }}</td><td>{{ rtrim(rtrim(number_format((float) $line->quantity, 2), '0'), '.') }}</td><td class="number">${{ number_format((float) $line->amount, 2) }}</td></tr>@endforeach
    </tbody></table>
    <h2>Payment history and receipts</h2>
    <table><thead><tr><th>Receipt</th><th>Date</th><th>Method / details</th><th>Explanation / notes</th><th class="number">Payment</th><th class="number">Balance after</th></tr></thead><tbody>
        @php($runningPaid = 0)
        @foreach ($job->payments as $payment)
            @php($runningPaid += (float) $payment->amount)
            <tr>
                <td>{{ $payment->receipt_no }}</td>
                <td>{{ $payment->transaction_date->format('M j, Y') }}</td>
                <td>{{ ucfirst($payment->payment_method) }}{{ $payment->check_number ? ' · Check ' . $payment->check_number : '' }}{{ $payment->check_date ? ' · Check date ' . $payment->check_date->format('M j, Y') : '' }}{{ $payment->card_last_four ? ' · Card •••• ' . $payment->card_last_four : '' }}{{ $payment->card_expiry_month && $payment->card_expiry_year ? ' · Exp ' . sprintf('%02d/%04d', $payment->card_expiry_month, $payment->card_expiry_year) : '' }}{{ $payment->account_reference ? ' · ' . $payment->account_reference : '' }}</td>
                <td>{{ $payment->explanation ?: '—' }}{{ $payment->notes ? ' · Notes: ' . $payment->notes : '' }}</td>
                <td class="number">${{ number_format((float) $payment->amount, 2) }}</td>
                <td class="number">${{ number_format(max((float) $job->total - $runningPaid, 0), 2) }}</td>
            </tr>
        @endforeach
    </tbody></table>
    <table class="totals">
        <tr><td>Invoice total</td><td class="number">${{ number_format((float) $job->total, 2) }}</td></tr>
        <tr><td>Total paid</td><td class="number">− ${{ number_format((float) $job->amount_paid, 2) }}</td></tr>
        <tr class="grand"><td>Balance due</td><td class="number">${{ number_format((float) $job->balance, 2) }}</td></tr>
    </table>
    <div class="footer">This invoice reflects all payments recorded for the job. Each installment has its own receipt number.</div>
</body>
</html>
