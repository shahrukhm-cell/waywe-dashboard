<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\JobPayment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_the_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Recent jobs')
            ->assertSee('Today’s schedule')
            ->assertSee('Outstanding balance')
            ->assertSee('Payment collections');
    }

    public function test_ajax_collection_filter_returns_only_the_collection_report(): void
    {
        $this->actingAs(User::factory()->create());

        $this->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get(route('dashboard', ['all_dates' => 1]))
            ->assertOk()
            ->assertSee('id="payment-collections"', false)
            ->assertDontSee('Recent jobs');
    }

    public function test_collection_report_keeps_payments_for_deleted_jobs_and_links_to_archived_details(): void
    {
        $this->actingAs(User::factory()->create());
        $job = Job::create([
            'job_number' => 'J-ARCHIVED',
            'job_type' => 'job',
            'status' => 'completed',
            'site_full_name' => 'Archived Customer',
            'total' => 100,
            'amount_paid' => 25,
            'balance' => 75,
        ]);
        $job->payments()->create([
            'receipt_no' => 'R-2026-000201',
            'payment_method' => 'cash',
            'amount' => 25,
            'transaction_date' => today()->toDateString(),
        ]);
        $job->delete();

        $this->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get(route('dashboard', ['all_dates' => 1]))
            ->assertOk()
            ->assertSee(route('jobs.deleted.show', $job), false)
            ->assertSee('R-2026-000201')
            ->assertSee('Archived Customer')
            ->assertSee('Archived')
            ->assertDontSee('Missing required parameter');
    }

    public function test_dashboard_collection_report_filters_payments_by_date_method_and_job_status(): void
    {
        $this->actingAs(User::factory()->create());

        $outstandingJob = Job::create([
            'status' => 'completed',
            'job_type' => 'job',
            'total' => 100,
            'balance' => 75,
        ]);
        $paidJob = Job::create([
            'status' => 'paid',
            'job_type' => 'job',
            'total' => 50,
            'amount_paid' => 50,
            'balance' => 0,
        ]);
        $olderJob = Job::create([
            'status' => 'paid',
            'job_type' => 'job',
            'total' => 100,
            'amount_paid' => 100,
            'balance' => 0,
        ]);

        $outstandingJob->payments()->create([
            'receipt_no' => 'R-2026-000101',
            'payment_method' => 'cash',
            'amount' => 25,
            'transaction_date' => today()->toDateString(),
        ]);
        $paidJob->payments()->create([
            'receipt_no' => 'R-2026-000102',
            'payment_method' => 'card',
            'amount' => 50,
            'transaction_date' => today()->subDay()->toDateString(),
        ]);
        $olderJob->payments()->create([
            'receipt_no' => 'R-2026-000103',
            'payment_method' => 'cash',
            'amount' => 100,
            'transaction_date' => today()->subMonth()->toDateString(),
        ]);

        $response = $this->get(route('dashboard', [
            'from' => today()->subDays(2)->toDateString(),
            'to' => today()->toDateString(),
        ]));
        $response->assertOk()
            ->assertViewHas('collectionTotal', 75.0)
            ->assertViewHas('collectionCount', 2)
            ->assertSee('R-2026-000101')
            ->assertSee('R-2026-000102')
            ->assertDontSee('R-2026-000103');

        $this->get(route('dashboard', ['all_dates' => 1]))
            ->assertOk()
            ->assertViewHas('collectionTotal', 175.0)
            ->assertViewHas('collectionCount', 3);

        $this->get(route('dashboard', [
            'from' => today()->subDays(2)->toDateString(),
            'to' => today()->toDateString(),
            'payment_method' => 'cash',
            'collection_status' => 'outstanding',
        ]))->assertOk()
            ->assertViewHas('collectionTotal', 25.0)
            ->assertViewHas('collectionCount', 1)
            ->assertSee('R-2026-000101')
            ->assertDontSee('R-2026-000102');

        $this->get(route('dashboard', [
            'from' => today()->subDays(2)->toDateString(),
            'to' => today()->toDateString(),
            'collection_status' => 'paid',
        ]))->assertOk()
            ->assertViewHas('collectionTotal', 50.0)
            ->assertViewHas('collectionCount', 1)
            ->assertSee('R-2026-000102')
            ->assertDontSee('R-2026-000101');
    }
}
