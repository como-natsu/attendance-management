<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class StaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']); // 認証 + 管理者権限
    }

    public function index()
    {
        // すべての一般ユーザーを取得
        $staffs = User::where('role', 'general')->get();

        return view('admin.staff.list', [
            'staffs' => $staffs,
        ]);
    }

    public function attendance(Request $request, $id)
    {
        $staff = User::findOrFail($id);

        $month = $request->query('month', now()->format('Y-m'));
        $start = Carbon::parse($month)->startOfMonth();
        $end = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::where('user_id', $staff->id)
            ->whereBetween('work_date', [$start, $end])
            ->get()
            ->keyBy(fn($item) => $item->work_date->format('Y-m-d'));

        $prevMonth = $start->copy()->subMonth()->format('Y-m');
        $nextMonth = $start->copy()->addMonth()->format('Y-m');

        return view('admin.staff.attendance-list', compact(
            'staff',
            'attendances',
            'start',
            'end',
            'prevMonth',
            'nextMonth'
        ));
    }

    public function export(Request $request,$id)
{
    $month = $request->query('month');
    $userId = $id;

    $start = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
    $end   = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

    $attendances = Attendance::where('user_id', $userId)
        ->whereBetween('work_date', [$start, $end])
        ->orderBy('work_date')
        ->get();

    $user = User::findOrFail($userId);
    $userName = $user->name;

    $csvHeader = [
        '日付',
        '出勤時間',
        '退勤時間',
        '休憩時間',
        '勤務時間',
    ];

    $rows = [];
    $rows[] = $csvHeader;

    foreach ($attendances as $attendance) {
        $rows[] = [
            $attendance->work_date->format('Y/m/d'),
            optional($attendance->clock_in)->format('H:i'),
            optional($attendance->clock_out)->format('H:i'),
            $attendance->break_total,
            $attendance->work_total,
        ];
    }

    $stream = fopen('php://temp', 'r+b');
    foreach ($rows as $row) {
        fputcsv($stream, $row);
    }

    rewind($stream);
    $csv = stream_get_contents($stream);
    $csv = mb_convert_encoding($csv, 'SJIS-win', 'UTF-8');

    $filename = "{$userName}さんの勤怠リスト.csv";

    return response($csv, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename={$filename}",
    ]);
}

}
