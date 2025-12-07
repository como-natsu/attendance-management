<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function show_clock_out_button_when_status_is_working()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('退勤');
    }

    /** @test */
    public function user_can_clock_out()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);

        $response = $this->actingAs($user)->post('/attendance/clock-out');

        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => '退勤済',
        ]);
    }

    /** @test */
    public function clock_out_time_is_recorded_in_list_page()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);

        $this->actingAs($user)->post('/attendance/clock-out');

        $response = $this->actingAs($user)->get('/attendance/list');

        $formattedDate = \Carbon\Carbon::parse($attendance->work_date)
            ->locale('ja')
            ->isoFormat('MM/DD (ddd)');

        $response->assertSee($formattedDate);
    }
}
