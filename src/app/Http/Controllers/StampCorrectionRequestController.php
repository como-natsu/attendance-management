<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use Illuminate\Support\Facades\Auth;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'approval'); // デフォルトは承認待ち

        // 共通クエリ
        $query = AttendanceRequest::query();

        // タブごとのステータス条件
        if ($tab === 'approval') {
            $query->where('status', 'pending');
        } elseif ($tab === 'approved') {
            $query->whereIn('status', ['approved', 'rejected']);
        }

        if ($user->role === 'admin') {
            // 管理者は全ユーザーの申請を取得
            $requests = $query->latest()->get();

            // 管理者用 Blade を使用
            return view('admin.attendance-request.list', compact('tab', 'requests'));
        } else {
            // 一般ユーザーは自分の申請のみ
            $requests = $query->where('user_id', $user->id)->latest()->get();

            // 一般用 Blade を使用
            return view('stamp_correction_request.correction-request', compact('tab', 'requests'));
        }
    }

    public function detail($id)
    {
        $user = Auth::user();
        $requestItem = AttendanceRequest::with('attendance', 'attendance.breakTimes')
            ->findOrFail($id);

        // 権限チェック：一般ユーザーは自分の申請しか見れない
        if ($user->role !== 'admin' && $requestItem->user_id !== $user->id) {
            abort(403);
        }

        $attendance = $requestItem->attendance;
        $breaks = $attendance->breakTimes()->orderBy('id')->get();

        // Blade を役割ごとに分ける
        if ($user->role === 'admin') {
            return view('admin.attendance-request.approval', compact('attendance', 'requestItem', 'breaks'));
        } else {
            return view('attendance.detail', compact('attendance', 'requestItem', 'breaks'));
        }
    }

    public function showApproveForm($attendance_correct_request_id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {abort(403);
        }

        // 申請データを取得
        $requestItem = AttendanceRequest::with('attendance.breakTimes')->findOrFail($attendance_correct_request_id);

        $attendance = $requestItem->attendance;
        $breaks = $attendance->breakTimes()->orderBy('id')->get();

        return view('admin.attendance-request.approval', compact('requestItem', 'attendance', 'breaks'));
    }

    // 承認処理（管理者用）
    public function approve($attendance_correct_request_id)
    {
        $user = Auth::user();
        if ($user->role !== 'admin') {
            abort(403);
        }

        $requestItem = AttendanceRequest::findOrFail($attendance_correct_request_id);
        $requestItem->status = 'approved';
        $requestItem->save();

        return redirect()->route('admin.attendance-request.list')
            ->with('status', '申請を承認しました');
    }
}
