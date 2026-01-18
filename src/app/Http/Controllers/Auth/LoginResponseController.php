<?php

namespace App\Http\Controllers\Auth;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginResponseController implements LoginResponseContract
{
    /**
     * Handle the response after the user is authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toResponse($request)
    {
        $user = $request->user();

        // 管理者ページに一般ユーザーがログインしようとしたら弾く
        if ($request->is('admin/*') && $user->role !== 'admin') {
            Auth::logout();
            return redirect('/login')->withErrors(['email'=>'管理者専用ページです。']);
        }

        // メール未認証チェック
        if ($user instanceof MustVerifyEmail && ! $user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // role によるリダイレクト
        return $user->role === 'admin'
            ? redirect()->intended('/admin/attendance/list')
            : redirect()->intended('/attendance');
    }
}