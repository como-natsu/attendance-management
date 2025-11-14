<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'clock_in',
        'clock_out',
        'total_work_time',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function attendanceRequests()
    {
        return $this->hasMany(AttendanceRequest::class);
    }

    // 休憩合計を "HH:MM" 形式で取得
    public function getBreakTotalAttribute()
    {
        $totalMinutes = $this->breakTimes->sum(function($break) {
            if ($break->break_start && $break->break_end) {
                return \Carbon\Carbon::parse($break->break_end)
                    ->diffInMinutes(\Carbon\Carbon::parse($break->break_start));
            }
            return 0;
        });

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        return $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
    }

    // 勤務合計を "HH:MM" 形式で取得（休憩時間を差し引く）
    public function getWorkTotalAttribute()
    {
        if ($this->clock_in && $this->clock_out) {
            $totalMinutes = \Carbon\Carbon::parse($this->clock_out)
                ->diffInMinutes(\Carbon\Carbon::parse($this->clock_in));

            // 休憩時間（分）を差し引く
            $breakMinutes = 0;
            if ($this->break_total) {
                [$h, $m] = explode(':', $this->break_total);
                $breakMinutes = $h * 60 + $m;
            }

            $workMinutes = $totalMinutes - $breakMinutes;

            $hours = floor($workMinutes / 60);
            $minutes = $workMinutes % 60;

            return $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT);
            }

        return null;
    }

    public function calculateWorkTime()
    {
    if (!$this->clock_in || !$this->clock_out) {
        return;
    }

    // 総勤務時間（分）
    $total = Carbon::parse($this->clock_in)->diffInMinutes($this->clock_out);

    // 休憩を引く
    foreach ($this->breakTimes as $break) {
        if ($break->break_start && $break->break_end) {
            $total -= Carbon::parse($break->break_start)->diffInMinutes($break->break_end);
        }
    }

    $this->total_work_time = $total;
    $this->save();
    }
}
