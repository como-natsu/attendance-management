<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function shows_all_attendance_for_logged_in_user()
    {
        // ログインユーザーの勤怠
        $user = User::factory()->create();
        $this->actingAs($user);

        $attendances = Attendance::factory()->count(5)->create([
            'user_id' => $user->id,
            'work_date' => now(), // すべて今日の日付にしてもOK
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->get('/attendance/list');

        foreach ($attendances as $attendance) {
            $response->assertSee(Carbon::parse($attendance->clock_in)->format('H:i'));
            $response->assertSee(Carbon::parse($attendance->clock_out)->format('H:i'));
        }

        // 他ユーザーの勤怠は表示されない
        $otherUser = User::factory()->create();
        $otherAttendance = Attendance::factory()->create([
            'user_id' => $otherUser->id,
            'work_date' => now()->addMonth(), // 今月と被らない日付にする
            'clock_in' => '10:00:00',
            'clock_out' => '19:00:00',
        ]);

        // 他ユーザー勤怠の日付が画面に出ていないことを確認
        $response->assertDontSee($otherAttendance->work_date->format('m/d'));
    }


    /** @test */
    public function shows_current_month_on_page_load()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance/list');

        $currentMonth = now()->format('Y年m月'); // ビューの表示形式
        $response->assertSee($currentMonth);
    }

    /** @test */
    public function shows_previous_month_when_clicking_previous()
    {
        $user = User::factory()->create();

        $prevMonth = now()->subMonth();
        $response = $this->actingAs($user)->get('/attendance/list?month=' . $prevMonth->format('Y-m'));

        $formattedPrevMonth = $prevMonth->format('Y年m月');
        $response->assertSee($formattedPrevMonth);
    }

    /** @test */
    public function shows_next_month_when_clicking_next()
    {
        $user = User::factory()->create();

        $nextMonth = now()->addMonth();
        $response = $this->actingAs($user)->get('/attendance/list?month=' . $nextMonth->format('Y-m'));

        $formattedNextMonth = $nextMonth->format('Y年m月');
        $response->assertSee($formattedNextMonth);
    }

    /** @test */
    public function links_to_detail_page_for_each_attendance()
    {
        $user = User::factory()->create();

        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'work_date' => '2025-12-07',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('/attendance/detail/' . $attendance->id);
    }
}
