<?php

namespace App\Http\Controllers;

use App\Models\User;

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
}
