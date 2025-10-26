<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 今日の日付を取得
        //$today = now()->toDateString();// ← 本番用（自動で今日の日付）

        // ★ テスト用に特定の日付を指定したい場合はこちらを使う
        $today = '2025-10-02'; // ← テスト用：日付固定(テストを繰り返したい場合次の日に変更して再テスト)

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

        //$today = now()->toDateString();
        $today = '2025-10-02'; // ← テスト用

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

        //$today = now()->toDateString();
        $today = '2025-10-02'; // ← テスト用

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

        //$today = now()->toDateString();
        $today = '2025-10-02'; // ← テスト用

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

        //$today = now()->toDateString();
        $today = '2025-10-02'; // ← テスト用

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance && $attendance->status === '休憩中') {
            $attendance->status = '出勤中';
            $attendance->save();
        }

        return redirect()->back();
    }

    public function list(Request $request)
    {
        $user = Auth::user();

        $month = $request->query('month', now()->format('Y-m'));
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::where('user_id', $user->id)
            ->whereBetween('work_date', [$start, $end])
            ->get()
            ->keyBy(function($item) {
                return \Carbon\Carbon::parse($item->work_date)->format('Y-m-d');
            });

        return view('attendance.list',[
            'attendances' => $attendances,
            'start' => $start,
            'end' => $end,
            'prevMonth' => $start->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $start->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function detail($id)
    {
    $attendance = Attendance::findOrFail($id);
    return view('attendance.detail', compact('attendance'));
    }
}