@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')
<div class="container">
    <h1>勤怠一覧（{{ $date }}）</h1>

    <div class="mb-3">
        {{-- 前日・翌日リンクは GET で送信 --}}
        <a href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}" class="btn btn-secondary">前日</a>
        <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}" class="btn btn-secondary">翌日</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ユーザー名</th>
                <th>出勤</th>
                <th>退勤</th>
                <th>休憩開始</th>
                <th>休憩終了</th>
                <th>勤務時間</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $attendance)
            <tr>
                <td>{{ $attendance->user->name ?? '不明' }}</td>
                <td>{{ $attendance->clock_in ?? '-' }}</td>
                <td>{{ $attendance->clock_out ?? '-' }}</td>
                <td>{{ $attendance->break_start ?? '-' }}</td>
                <td>{{ $attendance->break_end ?? '-' }}</td>
                <td>
                    @if($attendance->clock_in && $attendance->clock_out)
                        {{ \Carbon\Carbon::parse($attendance->clock_in)->diffInHours($attendance->clock_out) }}時間
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">勤怠データはありません</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
