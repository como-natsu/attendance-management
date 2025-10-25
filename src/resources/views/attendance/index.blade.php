@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')


<div class="attendance__content">
    <div class="attendance__panel">
        <p>{{ $attendance->status }}</p>
        <p>{{ now()->format('Y年m月d日') }}</p>
        <p>{{ $currentTime }}</p>
    </div>

    <div class="attendance__button">
        @if($attendance->status === '勤務外')
        <form action="{{ route('attendance.clock_in') }}" method="POST">
            @csrf
            <button class="attendance__button-submit" type="submit">出勤</button>
        </form>
        @elseif($attendance->status ==='出勤中')
        <form action="{{ route('attendance.break_start') }}" method="POST">
            @csrf
            <button class="attendance__button-submit" type="submit">休憩入</button>
        </form>
        <form action="{{ route('attendance.clock_out') }}" method="POST">
            @csrf
            <button class="attendance__button-submit" type="submit">退勤</button>
        </form>
        @elseif($attendance->status ==='休憩中')
        <form action="{{ route('attendance.break_end') }}" method="POST">
            @csrf
            <button class="attendance__button-submit" type="submit">休憩戻</button>
        </form>
        @elseif($attendance->status ==='退勤済')
        <p>お疲れ様でした。</p>
        @endif
    </div>

</div>
@endsection