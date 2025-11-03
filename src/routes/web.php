<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StampCorrectionRequestController;

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
Route::get('/login', function () {return view('auth.login');})->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
Route::middleware(['auth'])->group(function(){
    Route::get('/attendance',[AttendanceController::class,'index'])->name('attendance.index');
    Route::post('/attendance/clock-in',[AttendanceController::class,'clockIn'])->name('attendance.clock_in');
    Route::post('/attendance/clock-out',[AttendanceController::class,'clockOut'])->name('attendance.clock_out');
    Route::post('/attendance/break-start',[AttendanceController::class,'breakStart'])->name('attendance.break_start');
    Route::post('/attendance/break-end',[AttendanceController::class,'breakEnd'])->name('attendance.break_end');
    Route::get('/attendance/list',[AttendanceController::class,'list'])->name('attendance.list');
    Route::get('/attendance/detail/{id}',[AttendanceController::class,'detail'])->name('attendance.detail');
    Route::post('/attendance/detail/{id}/request-edit', [AttendanceController::class, 'requestEdit'])->name('attendance.requestEdit');
    Route::prefix('stamp_correction_request')->group(function () {
        Route::get('/list', [StampCorrectionRequestController::class, 'index'])->name('stamp_correction_request.list');
        Route::post('/store', [StampCorrectionRequestController::class, 'store'])->name('stamp_correction_request.store');
        Route::get('/detail/{id}', [StampCorrectionRequestController::class,'detail'])->name('stamp_correction_request.detail');
    });
});
