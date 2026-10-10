<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Quote {{ $job->quote_estimate_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1e293b; font-size: 12px; }
        .top { border-bottom: 2px solid #4f46e5; padding-bottom: 16px; margin-bottom: 20px; }
        .row { width: 100%; }
        .left { width: 60%; display: inline-block; vertical-align: top; }
        .right { width: 38%; display: inline-block; vertical-align: top; text-align: right; }
        h1 { margin: 0 0 6px; font-size: 24px; }
        .label { color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; }
        table { width: 100%; border-collapse: collapse; margin-top: 22px; }
        th, td { padding: 9px 7px; border-bottom: 1px solid #e2e8f0; text-align: left; }
        th { background: #f8fafc; color: #475569; font-size: 10px; text-transform: uppercase; }
        .number { text-align: right; }
        .totals { margin: 20px 0 0 auto; width: 240px; }
        .totals td { border: 0; padding: 5px; }
        .grand { border-top: 1px solid #cbd5e1 !important; font-weight: bold; font-size: 14px; }
        .note { margin-top: 25px; padding: 12px; background: #f8fafc; white-space: pre-line; }
        .footer { margin-top: 32px; border-top: 1px solid #e2e8f0; padding-top: 12px; color: #64748b; font-size: 10px; }
    </style>
</head>
<body>
    <div class="top">
        <div class="row">
            <div class="left"><div class="label">Service estimate</div><h1>Quote {{ $job->quote_estimate_number }}</h1><div>Prepared {{ $job->created_at->format('F j, Y') }}</div><div><strong>Expires {{ $job->created_at->copy()->addDays(30)->format('F j, Y') }}</strong></div></div>
            <div class="right"><strong>{{ $job->site_business ?: config('app.name') }}</strong><br>For: {{ $job->billing_full_name ?: $job->site_full_name ?: $job->customer?->full_name }}<br>{{ $job->billing_telephone ?: $job->site_telephone }}<br>{{ trim(collect([$job->billing_address ?: $job->site_address, $job->billing_city ?: $job->site_city, $job->billing_state ?: $job->site_state, $job->billing_postal_code ?: $job->site_postal_code])->filter()->implode(', ')) }}</div>
        </div>
    </div>
    <table>
        <thead><tr><th>Area / item</th><th>Description</th><th>Dimensions</th><th>Qty</th><th class="number">Amount</th></tr></thead>
        <tbody>
            @foreach ($job->lineItems as $line)
                <tr><td>{{ $line->area_item ?: '—' }}</td><td>{{ $line->description ?: '—' }}</td><td>{{ $line->length }} × {{ $line->width }}</td><td>{{ rtrim(rtrim(number_format((float) $line->quantity, 2), '0'), '.') }}</td><td class="number">${{ number_format((float) $line->amount, 2) }}</td></tr>
            @endforeach
        </tbody>
    </table>
    <table class="totals">
        <tr><td>Subtotal</td><td class="number">${{ number_format((float) $job->subtotal, 2) }}</td></tr>
        <tr><td>Tax</td><td class="number">${{ number_format((float) $job->tax, 2) }}</td></tr>
        <tr><td>Other tax</td><td class="number">${{ number_format((float) $job->other_tax, 2) }}</td></tr>
        <tr class="grand"><td>Quote total</td><td class="number">${{ number_format((float) $job->total, 2) }}</td></tr>
    </table>
    @if ($job->notes)<div class="note"><strong>Quote notes</strong><br>{{ $job->notes }}</div>@endif
    <div class="footer">This quote / estimate is valid for 30 days from the prepared date and expires on {{ $job->created_at->copy()->addDays(30)->format('F j, Y') }}. Work is scheduled and dispatched after approval. Payment is not recorded on this document.</div>
</body>
</html>
