@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <h2>勤怠詳細</h2>

    <form action="{{ route('attendance.requestEdit', $attendance->id) }}" method="POST">
        @csrf

        <!-- 名前 -->
        <div class="form-group">
            <label>名前</label>
            <span class="work-name">{{ $attendance->user->name }}</span>
        </div>

        <!-- 日付 -->

        <div class="form-group">
            <label>日付</label>
            <span class="work-year">{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y') }}年</span>
            <span class="work-month-day">{{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}</span>
        </div>

        <!-- 出勤・退勤 -->
        <div class="form-group">
            <label>出勤・退勤</label>
            <input type="text" name="clock_in"
                value="{{ old('clock_in', \Carbon\Carbon::parse($attendance->clock_in)->format('H:i')) }}" @if($request
                && $request->status === 'pending') disabled @endif>
            ～
            <input type="text" name="clock_out"
                value="{{ old('clock_out', \Carbon\Carbon::parse($attendance->clock_out)->format('H:i')) }}"
                @if($request && $request->status === 'pending') disabled @endif>
        </div>
        <div class="form-error">
            @error('clock_in')
            {{ $message }}
            @enderror
        </div>
        <div class="form-error">
            @error('clock_out')
            {{ $message }}
            @enderror
        </div>

        <!-- 休憩1 -->
        <div class="form-group">
            <label>休憩1</label>
            <input type="text" name="break1_start"
                value="{{ old('break1_start', isset($break1->break_start) ? \Carbon\Carbon::parse($break1->break_start)->format('H:i') : '') }}"
                @if($request && $request->status === 'pending') disabled @endif>
            ～
            <input type="text" name="break1_end"
                value="{{ old('break1_end', isset($break1->break_end) ? \Carbon\Carbon::parse($break1->break_end)->format('H:i') : '') }}"
                @if($request && $request->status === 'pending') disabled @endif>
        </div>
        <div class="form-error">
            @error('break1_start')
            {{ $message }}
            @enderror
        </div>
        <div class="form-error">
            @error('break1_end')
            {{ $message }}
            @enderror
        </div>


        <!-- 休憩2 -->
        <div class="form-group">
            <label>休憩2</label>
            <input type="text" name="break2_start"
                value="{{ old('break2_start', isset($break2->break_start) ? \Carbon\Carbon::parse($break2->break_start)->format('H:i') : '') }}"
                @if($request && $request->status === 'pending') disabled @endif>
            ～
            <input type="text" name="break2_end"
                value="{{ old('break2_end', isset($break2->break_end) ? \Carbon\Carbon::parse($break2->break_end)->format('H:i') : '') }}"
                @if($request && $request->status === 'pending') disabled @endif>
        </div>
        <div class="form-error">
            @error('break2_start')
            {{ $message }}
            @enderror
        </div>
        <div class="form-error">
            @error('break2_end')
            {{ $message }}
            @enderror
        </div>

        <!-- 備考 -->
        <div class="form-group">
            <label>備考</label>
            <textarea name="reason" @if($request &&
                $request->status === 'pending') disabled @endif>{{ old('reason', $request->reason ?? '') }}</textarea>
        </div>
        <div class="form-error">
            @error('reason')
            {{ $message }}
            @enderror
        </div>

        <!-- 修正ボタン -->
        @if(!$request || $request->status !== 'pending')
        <button type="submit">修正</button>
        @else
        <p style="color:red;">*承認待ちのため修正はできません。</p>
        @endif
    </form>
</div>
@endsection