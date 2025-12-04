<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\UpdateAttendanceRequest;

class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // 本番用：自動で今日の日付
        $today = now()->toDateString();
        // ★ テスト用：特定の日付を固定
        //$today = '2025-12-01';

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'work_date' => $today],
            ['status' => '勤務外']
        );

        return view('attendance.index', [
            'attendance' => $attendance,
        ]);
    }

    public function clockIn()
    {
        $user = Auth::user();
        $today = now()->toDateString(); // 本番用
        //$today = '2025-12-01'; // テスト用

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
        $today = now()->toDateString(); // 本番用
        //$today = '2025-12-01'; // テスト用

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance) {
            $attendance->status = '退勤済';
            $attendance->clock_out = now();
            $attendance->save();
        }

        $attendance?->calculateWorkTime();

        return redirect()->back();
    }

    public function breakStart()
    {
        $user = Auth::user();
        $today = now()->toDateString(); // 本番用
        //$today = '2025-12-01'; // テスト用

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance && $attendance->status === '出勤中') {
            $attendance->breakTimes()->create([
                'break_start' => now(),
                'break_end'   => null,
            ]);
            $attendance->status = '休憩中';
            $attendance->save();
        }

        return redirect()->back();
    }

    public function breakEnd()
    {
        $user = Auth::user();

        $today = now()->toDateString(); // 本番用
        //$today = '2025-12-01'; // テスト用

        $attendance = Attendance::where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($attendance && $attendance->status === '休憩中') {
            $lastBreak = $attendance->breakTimes()->latest()->first();
            if ($lastBreak && !$lastBreak->break_end) {
                $lastBreak->update(['break_end' => now()]);
            }
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
            ->keyBy(fn($item) => Carbon::parse($item->work_date)->format('Y-m-d'));

        return view('attendance.list', [
            'attendances' => $attendances,
            'start' => $start,
            'end' => $end,
            'prevMonth' => $start->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $start->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function detail($id)
    {
        $attendance = Attendance::with('breakTimes')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $breaks = $attendance->breakTimes()->orderBy('id')->get();

        $applyRequest = AttendanceRequest::where('attendance_id', $attendance->id)
            ->latest()
            ->first();

        return view('attendance.detail', compact('attendance', 'breaks','applyRequest'));
    }

    public function requestEdit(UpdateAttendanceRequest $request, $id)
    {
        $attendance = Attendance::where('user_id', auth()->id())->findOrFail($id);
        $workDate = Carbon::parse($attendance->work_date);

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
            'attendance_id'       => $attendance->id,
            'user_id'             => auth()->id(),
            'requested_clock_in'  => $clock_in,
            'requested_clock_out' => $clock_out,
            'requested_breaks'    => json_encode($breaks),
            'reason'              => $request->input('reason'),
            'status'              => 'pending',
        ]);

        return redirect()->route('attendance.detail', $attendance->id)
            ->with('status', '修正申請を送信しました（承認待ち）');
    }
}