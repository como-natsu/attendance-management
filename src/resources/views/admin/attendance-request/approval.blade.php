@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/approve.css') }}">
@endsection

@section('content')
<div class="admin-attendance-detail">
    <div class="admin-attendance-detail-title">
        <img class="admin-line-image" src="{{ asset('storage/image/Line.png') }}" alt="Line-image">
        <p class="admin-attendance-title-text">勤怠詳細</p>
    </div>

    <div class="admin-attendance-detail-content">

        {{-- 白い枠 --}}
        <div class="admin-form-group-wrapper">

            <!-- 名前 -->
            <div class="admin-form-group">
                <label class="admin-label">名前</label>
                <span class="admin-work-name">{{ $attendance->user->name }}</span>
            </div>
            <div class="admin-attendance-detail-row"></div>

            <!-- 日付 -->
            <div class="admin-form-group">
                <label class="admin-label">日付</label>
                <span class="admin-work-year">
                    {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y') }}年
                </span>
                <span class="admin-work-month-day">
                    {{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}
                </span>
            </div>
            <div class="admin-attendance-detail-row"></div>

            <!-- 出勤・退勤（表示専用） -->
            <div class="admin-form-group">
                <label class="admin-label">出勤・退勤</label>
                <div class="admin-input-block static-text">
                    {{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '--:--' }}
                    〜
                    {{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '--:--' }}
                </div>
            </div>
            <div class="admin-attendance-detail-row"></div>

            <!-- 休憩（表示専用） -->
            @foreach($breaks as $index => $break)
            <div class="admin-form-group">
                <label class="admin-label">休憩{{ $index + 1 }}</label>
                <div class="admin-input-block static-text">
                    {{ $break->break_start ? \Carbon\Carbon::parse($break->break_start)->format('H:i') : '--:--' }}
                    〜
                    {{ $break->break_end ? \Carbon\Carbon::parse($break->break_end)->format('H:i') : '--:--' }}
                </div>
            </div>
            <div class="admin-attendance-detail-row"></div>
            @endforeach

            <!-- 備考（表示専用） -->
            <div class="admin-form-group">
                <label class="admin-label">備考</label>
                <div class="admin-input-block static-text">
                    {{ $requestItem->reason ? $requestItem->reason : '（なし）' }}
                </div>
            </div>

        </div> <!-- admin-form-group-wrapper 終了 -->

        {{-- ボタン（白枠の外、右下に配置） --}}
        <div class="admin-attendance-detail-button-wrapper">
            @if($requestItem->status === 'pending')
            <form action="{{ route('admin.stamp_correction_request.approve', $requestItem->id) }}" method="POST">
                @csrf
                <button type="submit" class="admin-attendance-button">承認</button>
            </form>
            @else
            <div class="admin-attendance-button disabled-button">承認済み</div>
            @endif
        </div>

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