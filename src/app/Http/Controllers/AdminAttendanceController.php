<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;

class AdminAttendanceController extends Controller
{
    public function list(Request $request)
    {
        // 管理者は全ユーザーの勤怠を見られる前提
        $date = $request->query('date', now()->format('Y-m-d'));
        $day = Carbon::parse($date);

        // 1日の勤怠を取得
        $attendances = Attendance::whereDate('work_date', $day)->get();

        // 前日・翌日を計算
        $prevDate = $day->copy()->subDay()->format('Y-m-d');
        $nextDate = $day->copy()->addDay()->format('Y-m-d');

        return view('admin.attendance.list', [
            'attendances' => $attendances,
            'date' => $day->format('Y-m-d'),
            'prevDate' => $prevDate,
            'nextDate' => $nextDate,
    ]);
    }
}
