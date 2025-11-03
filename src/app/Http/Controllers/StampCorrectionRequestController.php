<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'approval'); // デフォルトは承認待ち

        if ($tab === 'approval') {
            // 承認待ち一覧
            $requests = AttendanceRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->get();
        } else {
            // 承認済み一覧
            $requests = AttendanceRequest::where('user_id', $user->id)
                ->whereIn('status', ['approved', 'rejected'])
                ->latest()
                ->get();
        }

        return view('stamp_correction_request.correction-request', compact('tab', 'requests'));
    }

    public function detail($id)
    {
        // 該当する申請データを取得
        $request = AttendanceRequest::where('user_id', auth()->id())
            ->findOrFail($id);

        // 対応する勤怠レコードを取得
        $attendance = Attendance::with('breakTimes')->findOrFail    ($request->attendance_id);

        // 休憩情報を取得
        $breaks = $attendance->breakTimes()->orderBy('id')->get();

        // 既存のattendance.detailビューをそのまま使う
        return view('attendance.detail', compact('attendance', 'request', 'breaks'));
    }
}