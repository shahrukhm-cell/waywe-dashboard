<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletedJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleted_jobs_can_be_searched_and_opened_as_read_only_full_details(): void
    {
        $this->actingAs(User::factory()->create());
        $customer = Customer::create([
            'first_name' => 'Jordan',
            'last_name' => 'Lee',
            'phone' => '555-0134',
        ]);
        $job = Job::create([
            'customer_id' => $customer->id,
            'job_number' => 'J-1001',
            'quote_estimate_number' => 'Q-1001',
            'job_type' => 'job',
            'status' => 'completed',
            'site_full_name' => 'Jordan Lee',
            'site_telephone' => '555-0134',
            'total' => 125,
            'subtotal' => 125,
            'balance' => 125,
            'notes' => 'Archived office note',
        ]);
        $job->lineItems()->create([
            'area_item' => 'Living room',
            'length' => 10,
            'width' => 12,
            'description' => 'Deep cleaning',
            'unit' => 'sq ft',
            'quantity' => 120,
            'time' => '01:00',
            'amount' => 125,
        ]);
        $job->delete();

        $this->get(route('jobs.deleted', ['search' => '555-0134']))
            ->assertOk()
            ->assertSee('J-1001')
            ->assertSee('View job')
            ->assertSee('Restore');

        $this->get(route('jobs.deleted.show', $job))
            ->assertOk()
            ->assertSee('Archived office note')
            ->assertSee('Living room')
            ->assertSee('Deep cleaning')
            ->assertSee('Restore job');
    }

    public function test_deleted_job_can_be_restored_to_the_active_jobs_list(): void
    {
        $this->actingAs(User::factory()->create());
        $job = Job::create([
            'job_number' => 'J-2001',
            'job_type' => 'job',
            'status' => 'completed',
            'site_full_name' => 'Restored Customer',
        ]);
        $job->delete();

        $this->post(route('jobs.restore', $job))
            ->assertRedirect(route('jobs.deleted'))
            ->assertSessionHas('success');

        $this->assertNull($job->fresh()->deleted_at);
        $this->assertDatabaseHas('service_jobs', [
            'id' => $job->id,
            'job_number' => 'J-2001',
            'deleted_at' => null,
        ]);
        $this->get(route('jobs.show', $job))->assertOk();
    }
}
