<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AttendanceRequest;

class AttendanceRequestFactory extends Factory
{
    protected $model = AttendanceRequest::class;

    public function definition()
    {
        return [
            'attendance_id' => null,
            'user_id' => null,
            'requested_clock_in' => $this->faker->dateTimeThisMonth(),
            'requested_clock_out' => $this->faker->dateTimeThisMonth(),
            'requested_breaks' => json_encode([['start' => '12:00', 'end' => '13:00']]),
            'reason' => $this->faker->sentence(),
            'status' => 'pending',
            'approver_id' => null,
            'decided_at' => null,
        ];
    }
}
