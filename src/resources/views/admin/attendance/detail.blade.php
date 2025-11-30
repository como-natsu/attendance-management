@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-detail.css') }}">
@endsection

@section('content')
<div class="admin-attendance-detail">
    <div class="admin-attendance-detail-title">
        <img class="admin-line-image" src="{{ asset('storage/image/Line.png') }}" alt="Line-image">
        <p class="admin-attendance-title-text">勤怠詳細</p>
    </div>

    <div class="admin-attendance-detail-content {{ !$canEdit ? 'admin-pending' : '' }}">
        <form action="{{ route('admin.attendance.requestEdit', $attendance->id) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="admin-form-group-wrapper">
                <div class="admin-form-group">
                    <label class="admin-label">名前</label>
                    <span class="admin-work-name">{{ $attendance->user->name }}</span>
                </div>
                <div class="admin-attendance-detail-row"></div>
                <div class="admin-form-group">
                    <label class="admin-label">日付</label>
                    <span class="admin-work-year">{{ $attendance->work_date->format('Y') }}年</span>
                    <span class="admin-work-month-day">{{ $attendance->work_date->format('n月j日') }}</span>
                </div>
                <div class="admin-attendance-detail-row"></div>

                @php
                $clockIn = $request && $request->requested_clock_in
                ? \Carbon\Carbon::parse($request->requested_clock_in)->format('H:i')
                : ($attendance->clock_in ? $attendance->clock_in->format('H:i') : '');

                $clockOut = $request && $request->requested_clock_out
                ? \Carbon\Carbon::parse($request->requested_clock_out)->format('H:i')
                : ($attendance->clock_out ? $attendance->clock_out->format('H:i') : '');
                @endphp

                <div class="admin-form-group">
                    <label class="admin-label">出勤・退勤</label>
                    <div class="admin-input-block">
                        <div class="admin-time-inputs">
                            <input type="text" name="clock_in" class="admin-time-input"
                                value="{{ old('clock_in', $clockIn) }}" @if(!$canEdit) disabled @endif>
                            <span>～</span>
                            <input type="text" name="clock_out" class="admin-time-input"
                                value="{{ old('clock_out', $clockOut) }}" @if(!$canEdit) disabled @endif>
                        </div>
                        <div class="form-error">
                            @error('clock_in') {{ $message }} @enderror
                            @error('clock_out') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
                <div class="admin-attendance-detail-row"></div>

                @php
                $breakData = $request && $request->requested_breaks
                ? json_decode($request->requested_breaks, true)
                : $breaks->map(fn($b) => [
                'break_start' => $b->break_start,
                'break_end' => $b->break_end
                ])->toArray();
                @endphp

                @foreach($breakData as $index => $break)
                <div class="admin-form-group">
                    <label class="admin-label">休憩{{ $index + 1 }}</label>
                    <div class="admin-input-block">
                        <div class="admin-time-inputs">
                            <input type="text" name="breaks[{{ $index }}][start]" class="admin-time-input"
                                value="{{ old("breaks.$index.start",
                                        $break['break_start'] ? \Carbon\Carbon::parse($break['break_start'])->format('H:i') : '') }}" @if(!$canEdit) disabled @endif>
                            <span>～</span>
                            <input type="text" name="breaks[{{ $index }}][end]" class="admin-time-input"
                                value="{{ old("breaks.$index.end",
                                        $break['break_end'] ? \Carbon\Carbon::parse($break['break_end'])->format('H:i') : '') }}" @if(!$canEdit) disabled @endif>
                        </div>
                    </div>
                </div>
                <div class="admin-attendance-detail-row"></div>
                @endforeach

                @php $nextIndex = count($breakData); @endphp

                @if($canEdit)
                <div class="admin-form-group">
                    <label class="admin-label">休憩{{ $nextIndex + 1 }}</label>
                    <div class="admin-input-block">
                        <div class="admin-time-inputs">
                            <input type="text" name="breaks[{{ $nextIndex }}][start]" class="admin-time-input"
                                value="{{ old("breaks.$nextIndex.start") }}">
                            <span>～</span>
                            <input type="text" name="breaks[{{ $nextIndex }}][end]" class="admin-time-input"
                                value="{{ old("breaks.$nextIndex.end") }}">
                        </div>
                    </div>
                </div>
                @endif
                <div class="admin-form-group">
                    <label class="admin-label">備考</label>
                    <div class="admin-input-block">
                        <textarea name="reason" class="admin-textarea" @if(!$canEdit) disabled
                            @endif>{{ old('reason', $request->reason ?? '') }}</textarea>
                        <div class="form-error">
                            @error('reason') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="admin-attendance-detail-button-wrapper">
                @if($canEdit)
                <button type="submit" class="admin-attendance-button">修正</button>
                @else
                <p class="admin-pending-message">＊承認待ちのため修正はできません。</p>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection