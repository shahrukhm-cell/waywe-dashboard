<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Job;
use App\Models\JobLineItem;
use App\Models\JobNote;
use App\Models\JobPayment;
use App\Models\JobTechnician;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class JobController extends Controller
{
    public function index()
    {
        $jobs = Job::with('customer')->latest()->get();

        return view('jobs.index', compact('jobs'));
    }

    public function calendar(Request $request)
    {
        $validated = $request->validate([
            'month' => ['nullable', 'date_format:Y-m'],
        ]);

        $month = isset($validated['month'])
            ? Carbon::createFromFormat('Y-m', $validated['month'])->startOfMonth()
            : today()->startOfMonth();

        $calendarStart = $month->copy()->startOfWeek(Carbon::SUNDAY);
        $calendarEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $jobs = Job::with('customer')
            ->whereRaw('COALESCE(booked_date, scheduled_date) BETWEEN ? AND ?', [
                $calendarStart->toDateString(),
                $calendarEnd->toDateString(),
            ])
            ->orderByRaw('COALESCE(booked_date, scheduled_date)')
            ->orderBy('scheduled_time')
            ->get()
            ->groupBy(fn (Job $job) => ($job->booked_date ?? $job->scheduled_date)->toDateString());

        $weeks = collect();
        $cursor = $calendarStart->copy();

        while ($cursor->lte($calendarEnd)) {
            $week = collect();

            for ($day = 0; $day < 7; $day++) {
                $dateKey = $cursor->toDateString();
                $dayJobs = $jobs->get($dateKey, collect());

                $week->push([
                    'date' => $cursor->copy(),
                    'jobs' => $dayJobs,
                    'count' => $dayJobs->count(),
                    'is_current_month' => $cursor->isSameMonth($month),
                    'is_today' => $cursor->isToday(),
                ]);

                $cursor->addDay();
            }

            $weeks->push($week);
        }

        $monthJobCount = $jobs
            ->flatten(1)
            ->filter(fn (Job $job) => ($job->booked_date ?? $job->scheduled_date)->isSameMonth($month))
            ->count();

        return view('jobs.calendar', [
            'month' => $month,
            'previousMonth' => $month->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $month->copy()->addMonth()->format('Y-m'),
            'weeks' => $weeks,
            'monthJobCount' => $monthJobCount,
        ]);
    }

    public function create(Request $request)
    {
        $customer = null;
        $phone = trim((string) $request->query('phone', ''));

        if ($phone !== '') {
            $customer = Customer::where('phone', $phone)->first();
        }

        return view('jobs.create', compact('customer', 'phone'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'site_full_name' => ['nullable', 'string', 'max:255'],
            'site_telephone' => ['required', 'string', 'max:255'],
            'site_business' => ['nullable', 'string', 'max:255'],
            'site_title' => ['nullable', 'string', 'max:255'],
            'site_address' => ['nullable', 'string', 'max:255'],
            'site_city' => ['nullable', 'string', 'max:255'],
            'site_state' => ['nullable', 'string', 'max:255'],
            'site_postal_code' => ['nullable', 'string', 'max:255'],
            'billing_full_name' => ['nullable', 'string', 'max:255'],
            'billing_telephone' => ['nullable', 'string', 'max:255'],
            'billing_business' => ['nullable', 'string', 'max:255'],
            'billing_title' => ['nullable', 'string', 'max:255'],
            'billing_address' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['nullable', 'string', 'max:255'],
            'billing_state' => ['nullable', 'string', 'max:255'],
            'billing_postal_code' => ['nullable', 'string', 'max:50'],
            'po_number' => ['nullable', 'string', 'max:255'],
            'confirmed_date' => ['nullable', 'date'],
            'booked_date' => ['required', 'date'],
            'job_type' => ['required', 'in:estimate,job,redo,pending,adjustment,delivery'],
            'quote_estimate_number' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array'],
            'items.*.area_item' => ['nullable', 'string', 'max:255'],
            'items.*.length' => ['nullable', 'numeric'],
            'items.*.width' => ['nullable', 'numeric'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.quantity' => ['nullable', 'numeric'],
            'items.*.time' => ['nullable', 'date_format:H:i'],
            'items.*.amount' => ['nullable', 'numeric', 'min:0'],
            'scheduled_date' => ['nullable', 'date'],
            'scheduled_time' => ['nullable', 'date_format:H:i'],
            'best_time' => ['nullable', 'date_format:H:i'],
            'crew_route' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'source_percent' => ['nullable', 'numeric', 'min:0'],
            'source_amount' => ['nullable', 'numeric', 'min:0'],
            'agent' => ['nullable', 'string', 'max:255'],
            'agent_percent' => ['nullable', 'numeric', 'min:0'],
            'agent_amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'other_tax' => ['nullable', 'numeric', 'min:0'],
            'technicians' => ['nullable', 'array'],
            'technicians.*.technician_name' => ['nullable', 'string', 'max:255'],
            'technicians.*.time' => ['nullable', 'date_format:H:i'],
            'technicians.*.amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $customer = $this->resolveCustomer($request);
        $bookedDate = $validated['booked_date'];
        $scheduledTime = $this->firstItemTime($request->input('items', [])) ?? ($validated['scheduled_time'] ?? null);

        $job = Job::create([
            'customer_id' => $customer?->id,
            'quote_estimate_number' => $validated['quote_estimate_number'] ?? $this->generateQuoteNumber(),
            'po_number' => $validated['po_number'] ?? null,
            'job_type' => $validated['job_type'],
            'status' => 'quote',
            'confirmed_date' => today()->toDateString(),
            'booked_date' => $bookedDate,
            'site_full_name' => $validated['site_full_name'] ?? $customer?->full_name,
            'site_telephone' => $validated['site_telephone'] ?? $customer?->phone,
            'site_business' => $validated['site_business'] ?? $customer?->business,
            'site_title' => $validated['site_title'] ?? $customer?->title,
            'site_address' => $validated['site_address'] ?? $customer?->address,
            'site_city' => $validated['site_city'] ?? $customer?->city,
            'site_state' => $validated['site_state'] ?? $customer?->state,
            'site_postal_code' => $validated['site_postal_code'] ?? $customer?->postal_code,
            'billing_full_name' => $validated['billing_full_name'] ?? $validated['site_full_name'] ?? $customer?->full_name,
            'billing_telephone' => $validated['billing_telephone'] ?? $validated['site_telephone'] ?? $customer?->phone,
            'billing_business' => $validated['billing_business'] ?? $validated['site_business'] ?? $customer?->business,
            'billing_title' => $validated['billing_title'] ?? $validated['site_title'] ?? $customer?->title,
            'billing_address' => $validated['billing_address'] ?? $validated['site_address'] ?? $customer?->address,
            'billing_city' => $validated['billing_city'] ?? $validated['site_city'] ?? $customer?->city,
            'billing_state' => $validated['billing_state'] ?? $validated['site_state'] ?? $customer?->state,
            'billing_postal_code' => $validated['billing_postal_code'] ?? $validated['site_postal_code'] ?? $customer?->postal_code,
            'crew_route' => $validated['crew_route'] ?? null,
            'scheduled_day' => date('D', strtotime($bookedDate)),
            'scheduled_date' => $bookedDate,
            'scheduled_time' => $scheduledTime,
            'best_time' => $validated['best_time'] ?? null,
            'source' => $validated['source'] ?? null,
            'source_percent' => $validated['source_percent'] ?? 0,
            'source_amount' => $validated['source_amount'] ?? 0,
            'agent' => $validated['agent'] ?? null,
            'agent_percent' => $validated['agent_percent'] ?? 0,
            'agent_amount' => $validated['agent_amount'] ?? 0,
            'tax' => $validated['tax'] ?? 0,
            'other_tax' => $validated['other_tax'] ?? 0,
            'notes' => $validated['notes'] ?? null,
        ]);

        $job->lineItems()->delete();
        $this->storeItems($job, $request->input('items', []));
        if ($request->has('technicians')) {
            $this->storeTechnicians($job, $request->input('technicians', []));
        }
        $this->refreshTotals($job);

        return redirect()->route('jobs.show', $job)->with('success', 'Quote saved. Download it from the job details when ready.');
    }

    public function show(Job $job)
    {
        $job->load(['customer', 'lineItems', 'reminders', 'technicians', 'payments']);

        return view('jobs.show', compact('job'));
    }

    public function edit(Job $job)
    {
        $job->load(['customer', 'lineItems', 'reminders', 'technicians']);

        return view('jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        $validated = $request->validate([
            'quote_estimate_number' => ['nullable', 'string', 'max:255'],
            'site_full_name' => ['nullable', 'string', 'max:255'],
            'site_telephone' => ['required', 'string', 'max:255'],
            'site_business' => ['nullable', 'string', 'max:255'],
            'site_title' => ['nullable', 'string', 'max:255'],
            'site_address' => ['nullable', 'string', 'max:255'],
            'site_city' => ['nullable', 'string', 'max:255'],
            'site_state' => ['nullable', 'string', 'max:255'],
            'site_postal_code' => ['nullable', 'string', 'max:255'],
            'billing_full_name' => ['nullable', 'string', 'max:255'],
            'billing_telephone' => ['nullable', 'string', 'max:255'],
            'billing_business' => ['nullable', 'string', 'max:255'],
            'billing_title' => ['nullable', 'string', 'max:255'],
            'billing_address' => ['nullable', 'string', 'max:255'],
            'billing_city' => ['nullable', 'string', 'max:255'],
            'billing_state' => ['nullable', 'string', 'max:255'],
            'billing_postal_code' => ['nullable', 'string', 'max:50'],
            'po_number' => ['nullable', 'string', 'max:255'],
            'confirmed_date' => ['nullable', 'date'],
            'booked_date' => ['required', 'date'],
            'crew_route' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'source_percent' => ['nullable', 'numeric', 'min:0'],
            'source_amount' => ['nullable', 'numeric', 'min:0'],
            'agent' => ['nullable', 'string', 'max:255'],
            'agent_percent' => ['nullable', 'numeric', 'min:0'],
            'agent_amount' => ['nullable', 'numeric', 'min:0'],
            'tax' => ['nullable', 'numeric', 'min:0'],
            'other_tax' => ['nullable', 'numeric', 'min:0'],
            'job_type' => ['required', 'in:estimate,job,redo,pending,adjustment,delivery'],
            'items' => ['nullable', 'array'],
            'items.*.area_item' => ['nullable', 'string', 'max:255'],
            'items.*.length' => ['nullable', 'numeric', 'min:0'],
            'items.*.width' => ['nullable', 'numeric', 'min:0'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.unit' => ['nullable', 'string', 'max:50'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'items.*.time' => ['nullable', 'date_format:H:i'],
            'items.*.amount' => ['nullable', 'numeric', 'min:0'],
            'scheduled_date' => ['nullable', 'date'],
            'scheduled_time' => ['nullable', 'date_format:H:i'],
            'best_time' => ['nullable', 'date_format:H:i'],
            'technicians' => ['nullable', 'array'],
            'technicians.*.technician_name' => ['nullable', 'string', 'max:255'],
            'technicians.*.time' => ['nullable', 'date_format:H:i'],
            'technicians.*.amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $bookedDate = $validated['booked_date'];
        $scheduledTime = $this->firstItemTime($request->input('items', [])) ?? ($validated['scheduled_time'] ?? $job->scheduled_time);

        $job->update([
            'quote_estimate_number' => $validated['quote_estimate_number'] ?? $job->quote_estimate_number,
            'po_number' => $validated['po_number'] ?? $job->po_number,
            'job_type' => $validated['job_type'],
            'status' => $job->status,
            'confirmed_date' => $validated['confirmed_date'] ?? $job->confirmed_date ?? today()->toDateString(),
            'booked_date' => $bookedDate,
            'site_full_name' => $validated['site_full_name'] ?? $job->site_full_name,
            'site_telephone' => $validated['site_telephone'] ?? $job->site_telephone,
            'site_business' => $validated['site_business'] ?? $job->site_business,
            'site_title' => $validated['site_title'] ?? $job->site_title,
            'site_address' => $validated['site_address'] ?? $job->site_address,
            'site_city' => $validated['site_city'] ?? $job->site_city,
            'site_state' => $validated['site_state'] ?? $job->site_state,
            'site_postal_code' => $validated['site_postal_code'] ?? $job->site_postal_code,
            'billing_full_name' => $validated['billing_full_name'] ?? $job->billing_full_name,
            'billing_telephone' => $validated['billing_telephone'] ?? $job->billing_telephone,
            'billing_business' => $validated['billing_business'] ?? $job->billing_business,
            'billing_title' => $validated['billing_title'] ?? $job->billing_title,
            'billing_address' => $validated['billing_address'] ?? $job->billing_address,
            'billing_city' => $validated['billing_city'] ?? $job->billing_city,
            'billing_state' => $validated['billing_state'] ?? $job->billing_state,
            'billing_postal_code' => $validated['billing_postal_code'] ?? $job->billing_postal_code,
            'crew_route' => $validated['crew_route'] ?? $job->crew_route,
            'scheduled_day' => date('D', strtotime($bookedDate)),
            'scheduled_date' => $bookedDate,
            'scheduled_time' => $scheduledTime,
            'best_time' => $validated['best_time'] ?? $job->best_time,
            'source' => $validated['source'] ?? $job->source,
            'source_percent' => $validated['source_percent'] ?? $job->source_percent,
            'source_amount' => $validated['source_amount'] ?? $job->source_amount,
            'agent' => $validated['agent'] ?? $job->agent,
            'agent_percent' => $validated['agent_percent'] ?? $job->agent_percent,
            'agent_amount' => $validated['agent_amount'] ?? $job->agent_amount,
            'tax' => $validated['tax'] ?? $job->tax,
            'other_tax' => $validated['other_tax'] ?? $job->other_tax,
            'notes' => $request->notes,
        ]);

        $job->lineItems()->delete();
        $this->storeItems($job, $request->input('items', []));
        if ($request->has('technicians')) {
            $this->storeTechnicians($job, $request->input('technicians', []));
        }
        $this->refreshTotals($job);

        return redirect()->route('jobs.show', $job)->with('success', 'Job updated successfully.');
    }

    public function confirm(Job $job)
    {
        if ($job->status !== 'quote' && $job->status !== 'draft') {
            return back()->with('success', 'This job has already been confirmed.');
        }

        $job->update([
            'job_number' => $job->job_number ?: $this->generateJobNumber(),
            'status' => 'approved',
            'confirmed_date' => now()->toDateString(),
        ]);

        return back()->with('success', 'Quote approved. The job number has been assigned.');
    }

    public function schedule(Request $request, Job $job)
    {
        if (! in_array($job->status, ['approved', 'scheduled', 'dispatched'], true)) {
            return back()->withErrors(['schedule' => 'Approve the quote before scheduling the job.']);
        }

        $validated = $request->validate([
            'crew_route' => ['nullable', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date'],
            'scheduled_time' => ['nullable', 'date_format:H:i'],
            'best_time' => ['nullable', 'date_format:H:i'],
            'technicians' => ['nullable', 'array'],
            'technicians.*.technician_name' => ['nullable', 'string', 'max:255'],
            'technicians.*.time' => ['nullable', 'date_format:H:i'],
            'technicians.*.amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $job->update([
            'status' => 'scheduled',
            'crew_route' => $validated['crew_route'] ?? null,
            'booked_date' => $validated['scheduled_date'],
            'scheduled_day' => date('D', strtotime($validated['scheduled_date'])),
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time' => $validated['scheduled_time'] ?? null,
            'best_time' => $validated['best_time'] ?? null,
        ]);
        $this->storeTechnicians($job, $validated['technicians'] ?? []);

        return back()->with('success', 'Schedule and technician assignments saved.');
    }

    public function markDispatched(Job $job)
    {
        abort_unless($job->status === 'scheduled' || $job->status === 'dispatched', 403, 'Schedule the approved job before dispatching it.');

        $job->update(['status' => 'dispatched']);

        return back()->with('success', 'Job marked as dispatched.');
    }

    public function complete(Job $job)
    {
        abort_unless(in_array($job->status, ['scheduled', 'dispatched', 'completed'], true), 403, 'The job must be scheduled or dispatched before it can be completed.');

        $job->update(['status' => 'completed']);

        return back()->with('success', 'Job marked complete. You can now record payments and issue the invoice.');
    }

    public function payment(Request $request, Job $job)
    {
        $availableBalance = max((float) $job->total - (float) $job->payments()->sum('amount'), 0);

        $validated = $request->validate([
            'payment_method' => ['required', Rule::in(['check', 'charge', 'card', 'cash', 'other'])],
            'amount' => ['required', 'numeric', 'gt:0', 'lte:' . number_format($availableBalance, 2, '.', '')],
            'transaction_date' => ['required', 'date'],
            'check_number' => ['nullable', 'string', 'max:100'],
            'check_date' => ['nullable', 'required_if:payment_method,check', 'date'],
            'account_reference' => [
                'nullable',
                'string',
                'max:40',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value !== null && ctype_digit((string) $value) && strlen((string) $value) !== 4) {
                        $fail('For account details, enter only the last four digits, not a full account number.');
                    }
                },
            ],
            'card_last_four' => ['required_if:payment_method,card,charge', 'nullable', 'digits:4'],
            'card_expiry_month' => ['nullable', 'required_with:card_expiry_year', 'integer', 'between:1,12'],
            'card_expiry_year' => ['nullable', 'required_with:card_expiry_month', 'integer', 'between:' . now()->year . ',' . (now()->year + 20)],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $payment = DB::transaction(function () use ($job, $validated): JobPayment {
            $lockedJob = Job::whereKey($job->id)->lockForUpdate()->firstOrFail();
            $currentBalance = max((float) $lockedJob->total - (float) $lockedJob->payments()->sum('amount'), 0);

            if ((float) $validated['amount'] > $currentBalance) {
                throw ValidationException::withMessages([
                    'amount' => 'This payment is greater than the remaining invoice balance.',
                ]);
            }

            $payment = $lockedJob->payments()->create([
                ...$validated,
                'receipt_no' => 'TMP-' . bin2hex(random_bytes(16)),
            ]);
            $payment->update([
                'receipt_no' => 'R-' . now()->format('Y') . '-' . str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT),
            ]);

            $paid = (float) $lockedJob->payments()->sum('amount');
            $total = (float) $lockedJob->total;
            $lockedJob->update([
                'invoice_number' => $lockedJob->invoice_number ?: 'INV-' . now()->format('Y') . '-' . str_pad((string) $lockedJob->id, 6, '0', STR_PAD_LEFT),
                'invoice_total' => $total,
                'amount_paid' => $paid,
                'balance' => max($total - $paid, 0),
                'payment_method' => $payment->payment_method,
                'payment_reference' => $payment->account_reference ?: $payment->card_last_four ?: $payment->check_number,
                'receipt_no' => $payment->receipt_no,
                'transaction_date' => $payment->transaction_date,
                'status' => $paid >= $total ? 'paid' : 'completed',
            ]);

            return $payment;
        });

        return redirect()->route('jobs.show', $job)->with('success', 'Payment saved. Receipt ' . $payment->receipt_no . ' is ready; the invoice includes the full payment history.');
    }

    public function dispatch(Job $job)
    {
        $job->load(['customer', 'lineItems', 'technicians']);

        return view('jobs.dispatch', compact('job'));
    }

    public function printDispatch(Job $job)
    {
        $job->load(['customer', 'lineItems', 'technicians']);

        $pdf = Pdf::loadView('jobs.dispatch_pdf', compact('job'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('dispatch-' . ($job->job_number ?: $job->id) . '.pdf');
    }

    public function quote(Job $job)
    {
        $job->load(['customer', 'lineItems']);

        return view('jobs.quote', compact('job'));
    }

    public function downloadQuote(Job $job)
    {
        $job->load(['customer', 'lineItems']);

        return Pdf::loadView('jobs.quote_pdf', compact('job'))
            ->setPaper('a4', 'portrait')
            ->download('quote-' . ($job->quote_estimate_number ?: $job->id) . '.pdf');
    }

    public function invoice(Job $job)
    {
        $job->load(['customer', 'lineItems', 'payments']);

        abort_if($job->payments->isEmpty(), 404, 'An invoice is available after the first payment is recorded.');

        return view('jobs.invoice', compact('job'));
    }

    public function downloadInvoice(Job $job)
    {
        $job->load(['customer', 'lineItems', 'payments']);

        abort_if($job->payments->isEmpty(), 404, 'An invoice is available after the first payment is recorded.');

        return Pdf::loadView('jobs.invoice_pdf', compact('job'))
            ->setPaper('a4', 'portrait')
            ->download('invoice-' . $job->invoice_number . '.pdf');
    }

    public function storeNote(Request $request, Job $job)
    {
        $validated = $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $job->reminders()->create($validated);

        return back()->with('success', 'Reminder added to this job.');
    }

    public function destroy(Job $job)
    {
        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job moved to deleted jobs.');
    }

    public function deleted(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $search = trim($validated['search'] ?? '');
        $jobs = Job::onlyTrashed()
            ->with('customer')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($jobQuery) use ($search) {
                    $jobQuery
                        ->where('job_number', 'like', '%' . $search . '%')
                        ->orWhere('quote_estimate_number', 'like', '%' . $search . '%')
                        ->orWhere('site_full_name', 'like', '%' . $search . '%')
                        ->orWhere('site_telephone', 'like', '%' . $search . '%')
                        ->orWhere('site_business', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery
                                ->where('first_name', 'like', '%' . $search . '%')
                                ->orWhere('last_name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%')
                                ->orWhere('business', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest('deleted_at')
            ->paginate(15)
            ->withQueryString();

        return view('jobs.deleted', compact('jobs', 'search'));
    }

    public function showDeleted(Job $job)
    {
        abort_unless($job->trashed(), 404);

        $job->load(['customer', 'lineItems', 'reminders', 'technicians', 'payments']);

        return view('jobs.deleted-show', compact('job'));
    }

    public function restore(Job $job)
    {
        abort_unless($job->trashed(), 404);

        $job->restore();

        return redirect()->route('jobs.deleted')->with('success', 'Job restored successfully.');
    }

    protected function resolveCustomer(Request $request): ?Customer
    {
        if ($request->filled('customer_id')) {
            return Customer::find($request->customer_id);
        }

        $phone = trim((string) ($request->site_telephone ?? $request->customer_phone ?? ''));

        if ($phone === '') {
            return null;
        }

        $parts = $this->splitName($request->site_full_name ?? '');

        return Customer::firstOrCreate(
            ['phone' => $phone],
            [
                'first_name' => $parts['first_name'],
                'last_name' => $parts['last_name'],
                'business' => $request->site_business,
                'title' => $request->site_title,
                'address' => $request->site_address,
                'city' => $request->site_city,
                'state' => $request->site_state,
                'postal_code' => $request->site_postal_code,
            ]
        );
    }

    protected function storeItems(Job $job, array $items): void
    {
        foreach ($items as $item) {
            if (! is_array($item) || empty($item['description']) && empty($item['area_item'])) {
                continue;
            }

            JobLineItem::create([
                'job_id' => $job->id,
                'area_item' => $item['area_item'] ?? null,
                'length' => (float) ($item['length'] ?? 0),
                'width' => (float) ($item['width'] ?? 0),
                'description' => $item['description'] ?? null,
                'unit' => $item['unit'] ?? '1',
                'quantity' => isset($item['length'], $item['width']) && $item['length'] !== '' && $item['width'] !== ''
                    ? round((float) $item['length'] * (float) $item['width'], 2)
                    : (float) ($item['quantity'] ?? 1),
                'time' => $item['time'] ?? null,
                'amount' => (float) ($item['amount'] ?? 0),
            ]);
        }
    }

    protected function firstItemTime(array $items): ?string
    {
        foreach ($items as $item) {
            if (is_array($item) && ! empty($item['time'])) {
                return $item['time'];
            }
        }

        return null;
    }

    protected function storeTechnicians(Job $job, array $technicians): void
    {
        $job->technicians()->delete();

        foreach ($technicians as $technician) {
            if (! is_array($technician) || trim((string) ($technician['technician_name'] ?? '')) === '') {
                continue;
            }

            JobTechnician::create([
                'job_id' => $job->id,
                'technician_name' => trim($technician['technician_name']),
                'time' => $technician['time'] ?? null,
                'amount' => $technician['amount'] ?? 0,
            ]);
        }
    }

    protected function refreshTotals(Job $job): void
    {
        $subtotal = (float) $job->lineItems()->sum('amount');
        $tax = (float) $job->tax;
        $otherTax = (float) $job->other_tax;
        $total = $subtotal + $tax + $otherTax;
        $paid = (float) $job->payments()->sum('amount');

        $job->update([
            'subtotal' => $subtotal,
            'total' => $total,
            'invoice_total' => $total,
            'amount_paid' => $paid,
            'balance' => max($total - $paid, 0),
            'status' => $paid >= $total && $paid > 0 ? 'paid' : ($job->status === 'paid' ? 'completed' : $job->status),
        ]);
    }

    protected function splitName(?string $name): array
    {
        $name = trim((string) $name);

        if ($name === '') {
            return ['first_name' => '', 'last_name' => ''];
        }

        $parts = preg_split('/\s+/', $name, 2);

        return [
            'first_name' => $parts[0] ?? '',
            'last_name' => $parts[1] ?? '',
        ];
    }

    protected function generateQuoteNumber(): string
    {
        return 'Q-' . now()->format('YmdHis');
    }

    protected function generateJobNumber(): string
    {
        $highestJobNumber = Job::withTrashed()
            ->whereNotNull('job_number')
            ->pluck('job_number')
            ->map(fn ($jobNumber) => (int) filter_var($jobNumber, FILTER_SANITIZE_NUMBER_INT))
            ->max();
        $base = $highestJobNumber ?? 62775;

        return (string) ($base + 1);
    }
}
