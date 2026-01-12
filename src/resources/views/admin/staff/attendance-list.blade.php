@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/list.css') }}">
@endsection

@section('content')

<div class="attendance-content-list">
    <div class="attendance-content-title">
        <img class="line-image" src="{{ asset('storage/image/Line.png') }}" alt="Line-image">
        <p class="attendance-title">{{ $staff->name }}さんの勤怠</p>
    </div>

    <div class="month-navigation">
        <div class="month-left">
            <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $prevMonth]) }}"
                class="month-nav-link">
                <img src="{{ asset('storage/image/left arrow.png') }}" alt="前月" class="arrow-icon">
                前月
            </a>
        </div>

        <div class="month-center">
            <form action="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}" method="GET" class="month-form">
                <label class="calendar-label">
                    <img src="{{ asset('storage/image/calendar.png') }}" alt="カレンダー" class="calendar-icon">
                    <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}"
                        class="month-input" onchange="this.form.submit()">
                </label>
            </form>
            <span class="month-text">
                {{ \Carbon\Carbon::parse(request('month', now()->format('Y-m')))->format('Y年m月') }}
            </span>
        </div>

        <div class="month-right">
            <a href="{{ route('admin.staff.attendance', ['id' => $staff->id, 'month' => $nextMonth]) }}"
                class="month-nav-link">
                翌月
                <img src="{{ asset('storage/image/right arrow.png') }}" alt="翌月" class="arrow-icon">
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

        @foreach(\Carbon\CarbonPeriod::create($start, $end) as $date)
        @php
        $key = $date->format('Y-m-d');
        $attendance = $attendances->get($key);
        @endphp
        <tr class="attendance-table-row">
            <td class="attendance-table-item">
                {{ $date->format('m/d') }} ({{ ['日','月','火','水','木','金','土'][$date->dayOfWeek] }})
            </td>
            <td class="attendance-table-item">
                @if ($attendance?->clock_in)
                {{ $attendance->clock_in->format('H:i') }}
                @endif
            </td>
            <td class="attendance-table-item">
                @if ($attendance?->clock_out)
                {{ $attendance->clock_out->format('H:i') }}
                @endif
            </td>
            <td class="attendance-table-item">{{ $attendance?->break_total ?? '' }}</td>
            <td class="attendance-table-item">{{ $attendance?->work_total ?? '' }}</td>
            <td class="attendance-table-item">
                @if ($attendance)
                <a class="attendance-table-item-link"
                    href="{{ route('admin.attendance.detail', $attendance->id) }}">詳細</a>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    <div class="csv-button">
    <a
        href="{{ route('admin.staff.attendance.csv', [
            'id' => $staff->id,
            'month' => request('month', now()->format('Y-m'))
        ]) }}"
        class="csv-button__submit"
    >
        エクスポート
    </a>
</div>
</div>
@endsection