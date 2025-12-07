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
                <p class="attendance-date-text" id="current-date">
                    {{ now()->format('Y年m月d日') }}
                </p>
            </div>
            <div class="attendance-time">
                <p class="attendance-time-text" id="current-time">
                    {{ now()->format('H:i') }}
                </p>
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

<script>
function updateDateTime() {
    const now = new Date();
    // 日付
    const year = now.getFullYear();
    const month = (now.getMonth()+1).toString().padStart(2,'0');
    const day = now.getDate().toString().padStart(2,'0');
    const weekday = ['日','月','火','水','木','金','土'][now.getDay()];
    document.getElementById('current-date').textContent = `${year}年${month}月${day}日(${weekday})`;

    // 時刻（秒は表示しない）
    const hours = now.getHours().toString().padStart(2,'0');
    const minutes = now.getMinutes().toString().padStart(2,'0');
    document.getElementById('current-time').textContent = `${hours}:${minutes}`;
}

// 最初に表示
updateDateTime();

// 毎秒更新
setInterval(updateDateTime, 1000);
</script>

@endsection