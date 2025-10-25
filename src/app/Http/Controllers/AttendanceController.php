<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 今日の日付を取得
        $today = now()->toDateString();// ← 本番用（自動で今日の日付）

        // ★ テスト用に特定の日付を指定したい場合はこちらを使う
        // $today = '2025-10-01'; // ← テスト用：日付固定(テストを繰り返したい場合次の日に変更して再テスト)

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            ['status' => '勤務外']
        );

        return view('attendance.index', [
            'attendance' => $attendance,
            'currentTime' => now()->format('H:i'),
        ]);
    }

    public function clockIn()
    {
        $user = Auth::user();

        $today = now()->toDateString();
        // $today = '2025-10-01'; // ← テスト用

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            ['status' => '勤務外']
        );

        $attendance->status = '出勤中';
        $attendance->clock_in = now();
        $attendance->save();

        return redirect()->back();
    }

    public function clockOut()
    {
        $user = Auth::user();

        $today = now()->toDateString();
        // $today = '2025-10-01'; // ← テスト用

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance) {
            $attendance->status = '退勤済';
            $attendance->clock_out = now();
            $attendance->save();
        }

        return redirect()->back();
    }

    public function breakStart()
    {
        $user = Auth::user();

        $today = now()->toDateString();
        // $today = '2025-10-01'; // ← テスト用

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance && $attendance->status === '出勤中') {
            $attendance->status = '休憩中';
            $attendance->save();
        }

        return redirect()->back();
    }

    public function breakEnd()
    {
        $user = Auth::user();

        $today = now()->toDateString();
        // $today = '2025-10-01'; // ← テスト用

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance && $attendance->status === '休憩中') {
            $attendance->status = '出勤中';
            $attendance->save();
        }

        return redirect()->back();
    }
}