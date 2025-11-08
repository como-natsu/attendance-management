<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Http\Controllers\Auth\LoginResponseController;
use Laravel\Fortify\Contracts\LoginResponse;
use App\Http\Requests\LoginRequest;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use Illuminate\Support\Facades\Auth;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function () {
            return view('auth.register');
        });

        Fortify::loginView(function () {
            return request()->is('admin/login')
                ? view('auth.admin-login')
                : view('auth.login');
        });

        // 認証処理
        Fortify::authenticateUsing(function (Request $request) {
            $credentials = $request->only('email', 'password');

            // admin/login のときは管理者のみ
            if ($request->is('admin/*')) {
                $credentials['role'] = 'admin';
            } else {
                $credentials['role'] = 'general';
            }

            $user = Auth::getProvider()->retrieveByCredentials($credentials);

            if ($user && Auth::getProvider()->validateCredentials($user, $credentials)) {
            return $user;
            }

            return null;
        });

        $this->app->bind(FortifyLoginRequest::class, LoginRequest::class);

        // ログインレートリミット
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->email.$request->ip());
        });

        // ログイン後リダイレクトをカスタム
        $this->app->singleton(LoginResponse::class, LoginResponseController::class);
    }
}