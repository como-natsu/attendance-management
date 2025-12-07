<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_user_name()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create();

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertSee($user->name);
    }

    /** @test */
    public function shows_work_date()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create([
            'work_date' => Carbon::parse('2025-12-07')
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertSee($attendance->work_date->format('n月j日'));
    }

    /** @test */
    public function shows_clock_in_out()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create([
            'clock_in' => '2025-12-07 09:00:00',
            'clock_out' => '2025-12-07 18:00:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertSee(Carbon::parse($attendance->clock_in)->format('H:i'));
        $response->assertSee(Carbon::parse($attendance->clock_out)->format('H:i'));
    }

    /** @test */
    public function shows_break_time()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create();

        $break = BreakTime::factory()->for($attendance)->create([
            'break_start' => '2025-12-07 12:00:00',
            'break_end' => '2025-12-07 13:00:00',
        ]);

        $response = $this->actingAs($user)->get("/attendance/detail/{$attendance->id}");

        $response->assertSee(Carbon::parse($break->break_start)->format('H:i'));
        $response->assertSee(Carbon::parse($break->break_end)->format('H:i'));
    }
}
