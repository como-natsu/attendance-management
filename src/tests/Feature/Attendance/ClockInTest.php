<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Attendance;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function show_clock_in_button_when_status_is_off_duty()
    {
        $user = User::factory()->create();

        // 勤務外データ
        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '勤務外',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤');
    }

    /** @test */
    public function user_can_clock_in_successfully()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '勤務外',
        ]);

        // 出勤処理
        $response = $this->actingAs($user)->post('/attendance/clock-in');

        $response->assertRedirect('/attendance');

        // 出勤中になったか確認
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'status' => '出勤中',
        ]);
    }

    /** @test */
    public function clock_in_button_is_hidden_after_finished_status()
    {
        $user = User::factory()->create();

        // 退勤済みのデータ作成
        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '退勤済',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        // 出勤ボタンが見えない
        $response->assertDontSee('出勤');
    }

    /** @test */
    public function clock_in_time_is_recorded_in_list_page()
    {
        $user = User::factory()->create();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'status' => '勤務外',
        ]);

        // 出勤処理
        $this->actingAs($user)->post('/attendance/clock-in');

        $attendance = Attendance::where('user_id', $user->id)->first();

        // 一覧ページ確認
        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee($attendance->clock_in->format('H:i'));
    }
}
