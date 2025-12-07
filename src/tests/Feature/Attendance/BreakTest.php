<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function show_break_in_button_when_status_is_working()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩入');
    }

    /** @test */
    public function user_can_start_break()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);

        $response = $this->actingAs($user)->post('/attendance/break-start');

        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => '休憩中',
        ]);
    }

    /** @test */
    public function user_can_end_break()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '休憩中',
        ]);

        $response = $this->actingAs($user)->post('/attendance/break-end');

        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);
    }

    /** @test */
    public function break_time_is_recorded_in_list_page()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);

        $this->actingAs($user)->post('/attendance/break-start');
        $this->actingAs($user)->post('/attendance/break-end');

        $response = $this->actingAs($user)->get('/attendance/list');

        $formattedDate = \Carbon\Carbon::parse($attendance->work_date)
            ->locale('ja')
            ->isoFormat('MM/DD (ddd)');

        $response->assertSee($formattedDate);
    }
}