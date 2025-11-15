<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceRequest;

class AdminAttendanceController extends Controller
{
    public function list(Request $request)
    {
        // 管理者は全ユーザーの勤怠を見られる前提
        $date = $request->query('date', now()->format('Y-m-d'));
        $day = Carbon::parse($date);

        // 1日の勤怠を取得
        $attendances = Attendance::whereDate('work_date', $day)
            ->whereHas('user', fn($q) => $q->where('role', 'general'))
            ->get();

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

    // 勤怠詳細ページ（管理者用）
    public function detail($id)
    {
        // 勤怠と関連情報を取得
        $attendance = Attendance::with('user', 'breakTimes', 'attendanceRequests')
            ->findOrFail($id);

        $breaks = $attendance->breakTimes()->orderBy('id')->get();

        // 直近の申請状態を取得（同じ勤怠IDの最新レコード）
        $request = AttendanceRequest::where('attendance_id', $attendance->id)
            ->latest()
            ->first();

        return view('admin.attendance.detail', compact('attendance', 'breaks', 'request'));
    }

    // 修正申請送信（管理者用）
    public function requestEdit(Request $request, $id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        $workDate = Carbon::parse($attendance->work_date);

        // 入力値(H:i)をDATETIMEに変換
        $clock_in = $request->input('clock_in')
            ? Carbon::createFromFormat('H:i', $request->input('clock_in'))
                ->setDate($workDate->year, $workDate->month, $workDate->day)
            : null;

        $clock_out = $request->input('clock_out')
            ? Carbon::createFromFormat('H:i', $request->input('clock_out'))
                ->setDate($workDate->year, $workDate->month, $workDate->day)
            : null;

        $breakInputs = $request->input('breaks', []);
        $breaks = [];

        foreach ($breakInputs as $break) {
            if (!empty($break['start']) || !empty($break['end'])) {
                $breaks[] = [
                    'break_start' => !empty($break['start'])
                        ? Carbon::createFromFormat('H:i', $break['start'])
                            ->setDate($workDate->year, $workDate->month, $workDate->day)
                            ->toDateTimeString()
                        : null,
                    'break_end' => !empty($break['end'])
                        ? Carbon::createFromFormat('H:i', $break['end'])
                            ->setDate($workDate->year, $workDate->month, $workDate->day)
                            ->toDateTimeString()
                        : null,
                ];
            }
        }

        AttendanceRequest::create([
            'attendance_id'        => $attendance->id,
            'user_id'              => $attendance->user_id, // 勤怠ユーザーに紐づけ
            'requested_clock_in'   => $clock_in,
            'requested_clock_out'  => $clock_out,
            'requested_breaks'     => json_encode($breaks),
            'reason'               => $request->input('reason'),
            'status'               => 'pending',
        ]);

        return redirect()->route('admin.attendance.detail', $attendance->id)
            ->with('status', '修正申請を送信しました（承認待ち）');
    }
}
