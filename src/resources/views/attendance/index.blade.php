@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')

<div class="attendance-page">
    <div class="attendance-content">
        <div class="attendance-panel">
            <div class="attendance-status">
                <p class="attendance-status-text">{{ $attendance->status }}</p>
            </div>
            <div class="attendance-date">
                <p class="attendance-date-text">
                    {{ now()->format('Y年m月d日') }}({{ \Carbon\Carbon::now()->locale('ja')->isoFormat('ddd')}})</p>
            </div>
            <div class="attendance-time">
                <p class="attendance-time-text">{{ $currentTime }}</p>
            </div>
        </div>

        <div class="attendance-button">
            @if($attendance->status === '勤務外')
            <div class="attendance-button-single">
                <form action="{{ route('attendance.clock_in') }}" method="POST">
                    @csrf
                    <button class="attendance-button attendance-button-clock" type="submit">出勤</button>
                </form>
            </div>
            @elseif($attendance->status ==='出勤中')
            <div class="attendance-button-row">
                <form action="{{ route('attendance.clock_out') }}" method="POST">
                    @csrf
                    <button class="attendance-button attendance-button-clock" type="submit">退勤</button>
                </form>
                <form action="{{ route('attendance.break_start') }}" method="POST">
                    @csrf
                    <button class="attendance-button attendance-button-break" type="submit">休憩入</button>
                </form>
            </div>
            @elseif($attendance->status ==='休憩中')
            <div class="attendance-button-single">
                <form action="{{ route('attendance.break_end') }}" method="POST">
                    @csrf
                    <button class="attendance-button attendance-button-break" type="submit">休憩戻</button>
                </form>
            </div>
            @elseif($attendance->status ==='退勤済')
            <p>お疲れ様でした。</p>
            @endif
        </div>
    </div>
</div>
@endsection