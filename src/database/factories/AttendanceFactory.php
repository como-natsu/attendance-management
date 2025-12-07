<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Attendance;
use App\Models\User;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'work_date' => now()->toDateString(),
            'clock_in' => null,
            'clock_out' => null,
            'total_work_time' => null,
            'status' => '勤務外',
        ];
    }
}
