@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-list.css') }}">
@endsection

@section('content')
<div class="admin-attendance-list">
    <div class="admin-attendance-title">
        <img class="line-image" src="{{ asset('storage/image/Line.png') }}" alt="Line-image">
        <p class="attendance-title">
            {{ \Carbon\Carbon::parse($date)->format('Y年m月d日') }}の勤怠
        </p>
    </div>

    <div class="day-navigation">
        <div class="day-left">
            <a href="{{ route('admin.attendance.list', ['date' => $prevDate]) }}" class="day-nav-link">
                <img src="{{ asset('storage/image/left arrow.png') }}" alt="前日" class="arrow-icon">
                前日
            </a>
        </div>

        <div class="day-center">
            <form action="{{ route('admin.attendance.list') }}" method="GET" class="day-form">
                <label class="calendar-label">
                    <img src="{{ asset('storage/image/calendar.png') }}" alt="カレンダー" class="calendar-icon">
                    <input type="date" name="date" value="{{ request('date', now()->format('Y-m-d')) }}"
                        class="day-input" onchange="this.form.submit()">
                </label>
            </form>
            <span class="day-text">
                {{ \Carbon\Carbon::parse(request('date', now()->format('Y-m-d')))->format('Y年m月d日') }}
            </span>
        </div>

        <div class="day-right">
            <a href="{{ route('admin.attendance.list', ['date' => $nextDate]) }}" class="day-nav-link">
                翌日
                <img src="{{ asset('storage/image/right arrow.png') }}" alt="翌日" class="arrow-icon">
            </a>
        </div>
    </div>

    <table class="attendance-table-inner">
        <tr class="attendance-table-row">
            <th class="attendance-table-header">日付</th>
            <th class="attendance-table-header">出勤</th>
            <th class="attendance-table-header">退勤</th>
            <th class="attendance-table-header">休憩</th>
            <th class="attendance-table-header">合計</th>
            <th class="attendance-table-header">詳細</th>
        </tr>

        @foreach($attendances as $attendance)
        <tr class="attendance-table-row">
            <td class="attendance-table-item">{{ $attendance->user->name ?? '' }}</td>
            <td class="attendance-table-item">
                {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('G:i') : '' }}</td>
            <td class="attendance-table-item">
                {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('G:i') : '' }}</td>
            <td class="attendance-table-item">{{ $attendance->break_total ?? '' }}</td>
            <td class="attendance-table-item">{{ $attendance->work_total ?? '' }}</td>
            <td class="attendance-table-item">
                @if($attendance)
                <a class="attendance-table-item-link" href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection