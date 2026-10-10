<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobPayment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'payment_method' => ['nullable', Rule::in(['check', 'charge', 'card', 'cash', 'other'])],
            'collection_status' => ['nullable', Rule::in(['outstanding', 'paid'])],
            'all_dates' => ['sometimes', 'boolean'],
        ]);

        $from = $request->boolean('all_dates')
            ? null
            : ($request->exists('from') ? ($filters['from'] ?? null) : today()->startOfMonth()->toDateString());
        $to = $request->boolean('all_dates')
            ? null
            : ($request->exists('to') ? ($filters['to'] ?? null) : today()->toDateString());

        $jobs = Job::with('customer')->latest()->take(6)->get();
        $todayJobs = Job::with('customer')
            ->whereDate('scheduled_date', today())
            ->orderBy('scheduled_time')
            ->get();

        $paymentsQuery = JobPayment::query()
            ->with([
                'job' => fn ($query) => $query->withTrashed(),
                'job.customer',
            ])
            ->when($from, fn ($query) => $query->whereDate('transaction_date', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('transaction_date', '<=', $to))
            ->when($filters['payment_method'] ?? null, fn ($query, $method) => $query->where('payment_method', $method))
            ->when($filters['collection_status'] ?? null, function ($query, $status) {
                $query->whereHas('job', fn ($jobsQuery) => $jobsQuery->where(
                    'status',
                    $status === 'paid' ? 'paid' : 'completed'
                ));
            });

        $collectionTotal = (float) (clone $paymentsQuery)->sum('amount');
        $collectionCount = (clone $paymentsQuery)->count();
        $payments = $paymentsQuery
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'quotes' => Job::where('status', 'quote')->count(),
            'scheduled' => Job::where('status', 'scheduled')->count(),
            'completed' => Job::where('status', 'completed')->count(),
            'paid' => Job::where('status', 'paid')->count(),
            'outstanding' => (float) Job::where('balance', '>', 0)->sum('balance'),
            'collected' => $collectionTotal,
        ];

        if ($request->ajax()) {
            return view('dashboard.partials.collections', compact(
                'payments',
                'collectionTotal',
                'collectionCount',
                'from',
                'to',
                'filters'
            ));
        }

        return view('dashboard', compact(
            'jobs',
            'todayJobs',
            'stats',
            'payments',
            'collectionTotal',
            'collectionCount',
            'from',
            'to',
            'filters'
        ));
    }
}
