<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('role', 'general')->get();

        // 先月の1日〜末日まで
        $start = Carbon::now()->subMonth()->startOfMonth();
        $end = Carbon::now()->subMonth()->endOfMonth();

        foreach($users as $user){
            $date = $start->copy();

            while($date->lte($end)){

                // 日曜日はスキップ
                if ($date->isSunday()) {
                    $date->addDay();
                    continue;
                }

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'work_date' => $date->format('Y-m-d'),
                    'clock_in' => $date->copy()->setTime(9, 0, 0),
                    'clock_out' => $date->copy()->setTime(18, 0, 0),
                    'total_work_time' => 8,
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => $date->copy()->setTime(12, 0, 0),
                    'break_end' => $date->copy()->setTime(13, 0, 0),
                ]);

                $date->addDay();
            }
        }
    }
}
