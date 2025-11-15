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
}
