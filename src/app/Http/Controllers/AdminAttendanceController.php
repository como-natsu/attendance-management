<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use App\Http\Requests\AdminUpdateAttendanceRequest;

class AdminAttendanceController extends Controller
{
    public function list(Request $request)
    {
        // 管理者は全ユーザーの勤怠を見られる
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

    public function requestEdit(AdminUpdateAttendanceRequest $request, $id)
    {
        // 勤怠情報取得
        $attendance = Attendance::with('breakTimes')->findOrFail($id);
        $workDate = Carbon::parse($attendance->work_date);

        // 出勤・退勤を更新
        $attendance->clock_in  = Carbon::createFromFormat('H:i', $request->input('clock_in'))
            ->setDate($workDate->year, $workDate->month, $workDate->day);
        $attendance->clock_out = Carbon::createFromFormat('H:i',$request->input('clock_out'))
            ->setDate($workDate->year, $workDate->month, $workDate->day);
        $attendance->save();

        // 既存休憩を取得
        $existingBreaks = $attendance->breakTimes->keyBy('id');
        $breakInputs = $request->input('breaks', []);

        foreach ($breakInputs as $index => $break) {
            // start か end が入力されている場合のみ処理
            if (!empty($break['start']) || !empty($break['end'])) {

                $break_start = !empty($break['start'])
                    ? Carbon::createFromFormat('H:i', $break['start'])
                        ->setDate($workDate->year, $workDate->month, $workDate->day): null;

                $break_end = !empty($break['end'])
                    ? Carbon::createFromFormat('H:i', $break['end'])
                        ->setDate($workDate->year, $workDate->month, $workDate->day): null;

                // 既存休憩IDがある場合は更新、なければ新規作成
                if (!empty($break['id']) && $existingBreaks->has($break['id'])) {
                    $bt = $existingBreaks[$break['id']];
                    $bt->break_start = $break_start;
                    $bt->break_end   = $break_end;
                    $bt->save();
                    $existingBreaks->forget($break['id']); // 処理済みとして削除
                } else {
                    $attendance->breakTimes()->create([
                        'break_start' => $break_start,
                        'break_end'   => $break_end,
                    ]);
                }
            }
        }

        // 残った既存休憩は削除（フォームから消したもの）
        foreach ($existingBreaks as $bt) {
            $bt->delete();
        }

        // 修正理由は必須
        $reason = $request->input('reason');

        return redirect()->route('admin.attendance.detail', $attendance->id)
            ->with('status', '勤怠を修正しました');
    }
}
