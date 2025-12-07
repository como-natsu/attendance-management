<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class StatusAndTimeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function current_time_is_displayed_on_attendance_page()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $date = now()->format('Y年m月d日');
        $time = now()->format('H:i');

        $response->assertStatus(200);
        $response->assertSee($date);
        $response->assertSee($time);
    }

    /** @test */
    public function status_shows_off_duty_when_no_attendance_exists()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('勤務外');
    }

    /** @test */
    public function status_shows_working_when_clock_in_record_exists()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
    }

    /** @test */
    public function status_shows_breaking_when_break_record_exists()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '休憩中',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩中');
    }

    /** @test */
    public function status_shows_finished_when_clock_out_record_exists()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '退勤済',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤済');
    }
}
