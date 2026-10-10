<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobCalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_renders_jobs_as_direct_links_in_the_month_grid(): void
    {
        $this->actingAs(User::factory()->create());

        $job = Job::create([
            'job_number' => 'J-3001',
            'job_type' => 'job',
            'status' => 'scheduled',
            'scheduled_date' => '2026-10-15',
            'scheduled_time' => '09:30',
            'site_full_name' => 'Calendar Customer',
        ]);

        $this->get(route('jobs.calendar', ['month' => '2026-10']))
            ->assertOk()
            ->assertSee('grid-cols-7', false)
            ->assertSee(route('jobs.show', $job), false)
            ->assertSee('Calendar Customer')
            ->assertSee('09:30');

        $this->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('Calendar Customer');
    }

    public function test_calendar_collapses_extra_jobs_without_hiding_their_detail_links(): void
    {
        $this->actingAs(User::factory()->create());

        $jobs = collect(range(1, 5))->map(fn (int $number) => Job::create([
            'job_number' => 'J-31' . $number,
            'job_type' => 'job',
            'status' => 'scheduled',
            'scheduled_date' => '2026-10-15',
            'scheduled_time' => sprintf('%02d:00', 8 + $number),
            'site_full_name' => 'Calendar Customer ' . $number,
        ]));

        $response = $this->get(route('jobs.calendar', ['month' => '2026-10']))
            ->assertOk()
            ->assertSee('+2 more')
            ->assertSee('details')
            ->assertSee('Calendar Customer 1')
            ->assertSee('Calendar Customer 2')
            ->assertSee('Calendar Customer 3')
            ->assertSee('Calendar Customer 4')
            ->assertSee('Calendar Customer 5');

        foreach ($jobs as $job) {
            $response->assertSee(route('jobs.show', $job), false);
        }
    }

    public function test_a_job_scheduled_through_the_workflow_appears_on_the_calendar(): void
    {
        $this->actingAs(User::factory()->create());

        $job = Job::create([
            'job_type' => 'job',
            'status' => 'quote',
            'site_full_name' => 'Workflow Calendar Customer',
            'site_telephone' => '555-0199',
        ]);

        $this->post(route('jobs.confirm', $job))->assertRedirect();

        $this->post(route('jobs.schedule', $job), [
            'scheduled_date' => '2026-10-22',
            'scheduled_time' => '10:00',
        ])->assertRedirect();

        $this->assertSame('2026-10-22', $job->fresh()->scheduled_date->toDateString());

        $this->get(route('jobs.calendar', ['month' => '2026-10']))
            ->assertOk()
            ->assertSee('Workflow Calendar Customer')
            ->assertSee('10:00')
            ->assertSee(route('jobs.show', $job), false);
    }

    public function test_a_job_created_with_a_scheduled_date_appears_on_the_calendar(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('jobs.store'), [
            'job_type' => 'job',
            'site_full_name' => 'New Calendar Customer',
            'site_telephone' => '555-0198',
            'scheduled_date' => '2026-10-24',
            'scheduled_time' => '11:30',
        ])->assertRedirect();

        $job = Job::where('site_full_name', 'New Calendar Customer')->firstOrFail();

        $this->assertSame('2026-10-24', $job->scheduled_date->toDateString());

        $this->get(route('jobs.calendar', ['month' => '2026-10']))
            ->assertOk()
            ->assertSee('New Calendar Customer')
            ->assertSee('11:30')
            ->assertSee(route('jobs.show', $job), false);
    }

    public function test_calendar_includes_all_jobs_with_booked_dates(): void
    {
        $this->actingAs(User::factory()->create());

        $jobs = collect(range(1, 10))->map(fn (int $number) => Job::create([
            'job_number' => 'J-32' . $number,
            'job_type' => 'job',
            'status' => 'quote',
            'booked_date' => '2026-10-26',
            'site_full_name' => 'Booked Calendar Customer ' . $number,
        ]));

        $response = $this->get(route('jobs.calendar', ['month' => '2026-10']))
            ->assertOk()
            ->assertSee('10 jobs scheduled')
            ->assertSee('+7 more');

        foreach ($jobs as $number => $job) {
            $response->assertSee('Booked Calendar Customer ' . ($number + 1));
            $response->assertSee(route('jobs.show', $job), false);
        }
    }
}
