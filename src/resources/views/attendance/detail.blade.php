@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
<div class="attendance-detail">
    <div class="attendance-detail-title">
        <img class="line-image" src="{{ asset('storage/image/Line.png') }}" alt="Line-image">
        <p class="attendance-title">勤怠詳細</p>
    </div>

    <div class="attendance-detail-content">
        <form action="{{ route('attendance.requestEdit', $attendance->id) }}" method="POST">
            @csrf
            <div class="form-group-wrapper">

                <!-- 名前 -->
                <div class="form-group">
                    <label>名前</label>
                    <span class="work-name">{{ $attendance->user->name }}</span>
                </div>
                <div class="attendance-detail-row"></div>

                <!-- 日付 -->
                <div class="form-group">
                    <label>日付</label>
                    <span class="work-year">{{ \Carbon\Carbon::parse($attendance->work_date)->format('Y') }}年</span>
                    <span
                        class="work-month-day">{{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}</span>
                </div>
                <div class="attendance-detail-row"></div>

                <!-- 出勤・退勤 -->
                <div class="form-group">
                    <label>出勤・退勤</label>
                    <div class="input-block">
                        <div class="time-inputs">
                            <input type="text" name="clock_in" class="time-input"
                                value="{{ old('clock_in', $applyRequest && $applyRequest->requested_clock_in ? \Carbon\Carbon::parse($applyRequest->requested_clock_in)->format('H:i') : ($attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '')) }}"
                                @if($applyRequest && $applyRequest->status === 'pending') disabled @endif>

                            <span>～</span>

                            <input type="text" name="clock_out" class="time-input"
                                value="{{ old('clock_out', $applyRequest && $applyRequest->requested_clock_out ? \Carbon\Carbon::parse($applyRequest->requested_clock_out)->format('H:i') : ($attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '')) }}"
                                @if($applyRequest && $applyRequest->status === 'pending') disabled @endif>
                        </div>

                        <div class="form-error">
                            @error('clock_in') {{ $message }} @enderror
                            @error('clock_out') {{ $message }} @enderror
                        </div>
                    </div>
                </div>
                <div class="attendance-detail-row"></div>

                <!-- 休憩 -->
                @php
                $breakData = $applyRequest && !empty($applyRequest->requested_breaks)
                ? json_decode($applyRequest->requested_breaks, true)
                : $breaks->map(function($b){
                return [
                'break_start' => $b->break_start,
                'break_end' => $b->break_end
                ];
                })->toArray();
                @endphp

                @foreach($breakData as $index => $break)
                <div class="form-group">
                    <label>休憩{{ $index + 1 }}</label>
                    <div class="input-block">
                        <div class="time-inputs">
                            <input type="text" name="breaks[{{ $index }}][start]" class="time-input"
                                value="{{ old("breaks.$index.start", $break['break_start'] ? \Carbon\Carbon::parse($break['break_start'])->format('H:i') : '') }}"
                                @if($applyRequest && $applyRequest->status === 'pending') disabled @endif>

                            <span>～</span>

                            <input type="text" name="breaks[{{ $index }}][end]" class="time-input"
                                value="{{ old("breaks.$index.end", $break['break_end'] ? \Carbon\Carbon::parse($break['break_end'])->format('H:i') : '') }}"
                                @if($applyRequest && $applyRequest->status === 'pending') disabled @endif>
                        </div>

                        <div class="form-error">
                            @error("breaks.$index.start") {{ $message }} @enderror
                            @error("breaks.$index.end") {{ $message }} @enderror
                        </div>
                    </div>
                </div>
                <div class="attendance-detail-row"></div>
                @endforeach

                <!-- 追加休憩（承認待ちのときは非表示） -->
                @if(!$applyRequest || $applyRequest->status !== 'pending')
                @php $nextIndex = count($breakData); @endphp

                <div class="form-group">
                    <label>休憩{{ $nextIndex + 1 }}</label>
                    <div class="input-block">
                        <div class="time-inputs">
                            <input type="text" name="breaks[{{ $nextIndex }}][start]" class="time-input"
                                value="{{ old("breaks.$nextIndex.start") }}">

                            <span>～</span>

                            <input type="text" name="breaks[{{ $nextIndex }}][end]" class="time-input"
                                value="{{ old("breaks.$nextIndex.end") }}">
                        </div>
                    </div>
                </div>
                <div class="attendance-detail-row"></div>
                @endif

                <!-- 備考 -->
                <div class="form-group">
                    <label>備考</label>
                    <div class="input-block">
                        <textarea name="reason" @if($applyRequest &&
                            $applyRequest->status === 'pending') disabled @endif>{{ old('reason', $applyRequest->reason ?? '') }}</textarea>

                        <div class="form-error">
                            @error('reason') {{ $message }} @enderror
                        </div>
                    </div>
                </div>

            </div>

            <!-- 修正ボタン -->
            <div class="attendance-detail-button-wrapper">
                @if(!$applyRequest || $applyRequest->status !== 'pending')
                <button type="submit" class="attendance-button">修正</button>
                @else
                <p class="pending-message">*承認待ちのため修正はできません。</p>
                @endif
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateCurrentTime() {
    const now = new Date();
    const h = now.getHours().toString().padStart(2, '0');
    const m = now.getMinutes().toString().padStart(2, '0');
    document.querySelectorAll('.current-time').forEach(el => el.textContent = `${h}:${m}`);
}
updateCurrentTime();
setInterval(updateCurrentTime, 60000);
</script>
@endsection