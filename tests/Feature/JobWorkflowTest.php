<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_quote_can_be_approved_scheduled_dispatched_completed_and_paid_in_installments(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('jobs.store'), [
            'site_full_name' => 'Taylor Customer',
            'site_telephone' => '555-0100',
            'site_address' => '12 Main Street',
            'site_city' => 'Brooklyn',
            'job_type' => 'estimate',
            'items' => [[
                'area_item' => 'Area rug',
                'length' => 10,
                'width' => 15,
                'description' => 'Cleaning',
                'unit' => '1',
                'quantity' => 1,
                'time' => '01:30',
                'amount' => 150,
            ]],
        ])->assertRedirect();

        $job = Job::with('lineItems')->firstOrFail();
        $this->assertSame('quote', $job->status);
        $this->assertSame(150.0, (float) $job->lineItems->first()->quantity);
        $this->assertSame(150.0, (float) $job->total);
        $this->assertSame('12 Main Street', $job->billing_address);
        $this->assertSame('Brooklyn', $job->billing_city);
        $this->get(route('jobs.quote', $job))->assertOk()->assertSee('Service estimate');

        $this->post(route('jobs.confirm', $job))->assertRedirect();
        $job->refresh();
        $this->assertSame('approved', $job->status);
        $this->assertNotNull($job->job_number);
        $this->get(route('jobs.show', $job))->assertOk()->assertDontSee('Mark quote approved');

        $this->post(route('jobs.schedule', $job), [
            'crew_route' => 'Brooklyn',
            'scheduled_date' => now()->addDay()->toDateString(),
            'scheduled_time' => '09:00',
            'best_time' => '09:15',
            'technicians' => [
                ['technician_name' => 'Sam', 'time' => '09:00', 'amount' => 35],
            ],
        ])->assertRedirect();
        $job->refresh();
        $this->assertSame('scheduled', $job->status);
        $this->assertSame(1, $job->technicians()->count());
        $this->get(route('jobs.edit', $job))->assertOk();
        $this->put(route('jobs.update', $job), [
            'job_type' => 'estimate',
            'site_full_name' => 'Taylor Customer Updated',
            'items' => [[
                'area_item' => 'Area rug',
                'length' => 10,
                'width' => 15,
                'description' => 'Cleaning',
                'unit' => '1',
                'quantity' => 1,
                'time' => '01:30',
                'amount' => 150,
            ]],
        ])->assertRedirect();
        $job->refresh();
        $this->assertSame('scheduled', $job->status);
        $this->assertSame('Brooklyn', $job->crew_route);
        $this->assertSame('09:00', $job->scheduled_time);
        $this->assertSame(1, $job->technicians()->count());
        $this->get(route('jobs.dispatch', $job))->assertOk()->assertSee('Sam');

        $this->get(route('jobs.quote.download', $job))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->get(route('jobs.dispatch.print', $job))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->post(route('jobs.dispatch.mark', $job))->assertRedirect();
        $this->post(route('jobs.complete', $job))->assertRedirect();
        $job->refresh();
        $this->assertSame('completed', $job->status);

        $this->post(route('jobs.payment', $job), [
            'payment_method' => 'card',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
            'card_last_four' => '1234',
            'card_expiry_month' => 8,
            'card_expiry_year' => 2030,
            'account_reference' => '1234',
            'explanation' => 'Deposit received',
            'notes' => 'Installment one',
        ])->assertRedirect();
        $firstReceipt = $job->payments()->firstOrFail();
        $job->refresh();
        $this->assertSame(100.0, (float) $job->amount_paid);
        $this->assertSame(50.0, (float) $job->balance);
        $this->assertStringStartsWith('R-' . now()->format('Y') . '-', $firstReceipt->receipt_no);
        $this->assertSame('completed', $job->status);
        $this->get(route('jobs.invoice', $job))
            ->assertOk()
            ->assertSee($firstReceipt->receipt_no)
            ->assertSee('Exp 08/2030')
            ->assertSee('Deposit received')
            ->assertSee('Installment one')
            ->assertSee('Balance after payment');

        $this->post(route('jobs.payment', $job), [
            'payment_method' => 'cash',
            'amount' => 50,
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect();
        $job->refresh();
        $this->assertSame(2, $job->payments()->count());
        $this->assertSame(150.0, (float) $job->amount_paid);
        $this->assertSame(0.0, (float) $job->balance);
        $this->assertSame('paid', $job->status);

        $this->get(route('jobs.invoice.download', $job))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->post(route('jobs.notes.store', $job), ['note' => 'Call before arrival'])->assertRedirect();
        $this->post(route('jobs.notes.store', $job), ['note' => 'Leave invoice at the office'])->assertRedirect();
        $this->assertSame(2, $job->reminders()->count());
    }

    public function test_payment_cannot_store_a_full_numeric_card_or_account_number(): void
    {
        $this->actingAs(User::factory()->create());
        $job = Job::create([
            'status' => 'completed',
            'job_type' => 'job',
            'total' => 200,
            'invoice_total' => 200,
            'balance' => 200,
        ]);

        $this->post(route('jobs.payment', $job), [
            'payment_method' => 'card',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
            'account_reference' => '1234567890123456',
        ])->assertSessionHasErrors('account_reference');

        $this->assertSame(0, $job->payments()->count());
    }
}
