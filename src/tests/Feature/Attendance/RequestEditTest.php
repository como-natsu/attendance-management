<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\AttendanceRequest;

class RequestEditTest extends TestCase
{
    use RefreshDatabase;

    //** @test */
    public function clock_in_cannot_be_after_clock_out()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create([
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}/request-edit", [
            'clock_in' => '19:00',
            'clock_out' => '18:00',
            'reason' => '備考テスト',
        ]);

        $response->assertSessionHasErrors(['clock_in' => '出勤時間が不適切な値です']);
    }

    /** @test */
    public function break_start_cannot_be_after_clock_out()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create([
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}/request-edit", [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'breaks' => [
                ['start' => '19:00', 'end' => '19:30']
            ],
            'reason' => '備考テスト',
        ]);

        $response->assertSessionHasErrors(['breaks.0.start' => '休憩時間が不適切な値です']);
    }

    /** @test */
    public function break_end_cannot_be_after_clock_out()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create([
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}/request-edit", [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'breaks' => [
                ['start' => '12:00', 'end' => '19:00']
            ],
            'reason' => '備考テスト',
        ]);

        $response->assertSessionHasErrors(['breaks.0.end' => '休憩時間もしくは退勤時間が不適切な値です']);
    }

    /** @test */
    public function reason_is_required()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create([
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}/request-edit", [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'breaks' => [
                ['start' => '12:00', 'end' => '13:00']
            ],
            'reason' => '',
        ]);

        $response->assertSessionHasErrors(['reason' => '備考を記入してください']);
    }

    /** @test */
    public function valid_attendance_update_creates_request()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create([
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($user)->patch("/attendance/detail/{$attendance->id}/request-edit", [
            'clock_in' => '09:30',
            'clock_out' => '18:00',
            'breaks' => [
                ['start' => '12:30', 'end' => '13:30']
            ],
            'reason' => '修正申請テスト',
        ]);

        $response->assertRedirect(); // 修正申請後はどこかにリダイレクトされる想定
        $this->assertDatabaseHas('attendance_requests', [
            'attendance_id' => $attendance->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function request_list_shows_user_requests()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create();

        $request = $attendance->requests()->create([
            'user_id' => $user->id,
            'clock_in' => '09:30',
            'clock_out' => '18:00',
            'reason' => '修正申請テスト',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get('/attendance/request/list');
        $response->assertSee($request->reason);
        $response->assertSee('承認待ち');
    }

    /** @test */
    public function request_detail_redirects_to_attendance_detail()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->for($user)->create();

        $request = $attendance->requests()->create([
            'user_id' => $user->id,
            'clock_in' => '09:30',
            'clock_out' => '18:00',
            'reason' => '修正申請テスト',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get("/attendance/request/detail/{$request->id}");
        $response->assertRedirect("/attendance/detail/{$attendance->id}");
    }
}