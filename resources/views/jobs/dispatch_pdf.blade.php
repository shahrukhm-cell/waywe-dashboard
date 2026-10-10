<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Dispatch</title>
    <style>
        body { font-family: sans-serif; color: #1f2937; }
        .header { display: flex; justify-content: space-between; margin-bottom: 24px; }
        .title { font-size: 24px; font-weight: bold; text-transform: uppercase; }
        .meta { font-size: 12px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; font-size: 12px; }
        th { background: #f3f4f6; text-align: left; }
        .small { font-size: 12px; }
        .summary { margin-top: 24px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">Dispatching List</div>
            <div class="meta">{{ now()->format('M d, Y') }}</div>
        </div>
        <div>
            <div class="meta">Job #</div>
            <div style="font-size: 22px; font-weight: bold;">{{ $job->job_number ?: $job->quote_estimate_number ?: '—' }}</div>
        </div>
    </div>

    <div class="small">
        <div><strong>Client:</strong> {{ $job->site_full_name ?: '—' }}</div>
        <div><strong>Phone:</strong> {{ $job->site_telephone ?: '—' }}</div>
        <div><strong>Address:</strong> {{ trim(($job->site_address ?: '') . ' ' . ($job->site_city ?: '') . ' ' . ($job->site_state ?: '') . ' ' . ($job->site_postal_code ?: '')) ?: '—' }}</div>
        <div><strong>Crew / Route:</strong> {{ $job->crew_route ?: '—' }}</div>
        <div><strong>Date:</strong> {{ $job->scheduled_date?->format('M d, Y') ?: '—' }} &nbsp; <strong>Arrival window:</strong> {{ $job->scheduled_time ?: '—' }} &nbsp; <strong>Best Time:</strong> {{ $job->best_time ?: '—' }}</div>
        <div><strong>Assigned technicians:</strong> {{ $job->technicians->map(fn ($technician) => $technician->technician_name . ($technician->time ? ' · ' . $technician->time : '') . ((float) $technician->amount > 0 ? ' · $' . number_format((float) $technician->amount, 2) : ''))->join(', ') ?: 'Not assigned' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Area/Item</th>
                <th>Description</th>
                <th>Qty</th>
                <th>Time</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($job->lineItems as $line)
                <tr>
                    <td>{{ $line->area_item ?: '—' }}</td>
                    <td>{{ $line->description ?: '—' }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td>{{ $line->time ?: '—' }}</td>
                    <td>${{ number_format((float) $line->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div><strong>Notes:</strong> {{ $job->notes ?: 'No notes' }}</div>
        <div><strong>Total Price:</strong> ${{ number_format((float) $job->total, 2) }}</div>
        <div><strong>No. of Service Lines:</strong> {{ $job->lineItems->count() }}</div>
    </div>
</body>
</html>
