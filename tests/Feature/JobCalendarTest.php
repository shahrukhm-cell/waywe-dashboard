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
}
