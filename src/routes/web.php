<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LogoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::post('/register', [RegisteredUserController::class, 'store']);
Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::middleware(['auth'])->group(function(){
    Route::get('/attendance',[AttendanceController::class,'index'])->name('attendance.index');
});
