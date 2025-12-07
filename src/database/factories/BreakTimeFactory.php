<?php

namespace Database\Factories;

use App\Models\BreakTime;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class BreakTimeFactory extends Factory
{
    protected $model = BreakTime::class;

    public function definition()
    {
        $start = $this->faker->time('H:i:s');
        $end = date('H:i:s', strtotime($start) + 3600); // 1時間後

        return [
            'break_start' => Carbon::parse($start),
            'break_end' => Carbon::parse($end),
        ];
    }
}
