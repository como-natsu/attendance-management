@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin-correction-request.css') }}">
@endsection

@section('content')
<div class="admin-request-content-list">
    <div class="admin-request-content-title">
        <img class="line-image" src="{{ asset('storage/image/Line.png') }}" alt="Line-image">
        <p class="admin-request-title">申請一覧</p>
    </div>
    <div class="tab-menu">
        <a href="{{ url('/stamp_correction_request/list?tab=approval') }}"
            class="{{ $tab === 'approval' ? 'active' : '' }}">承認待ち</a>
        <a href="{{ url('/stamp_correction_request/list?tab=approved') }}"
            class="{{ $tab === 'approved' ? 'active' : '' }}">承認済み</a>
    </div>

    <table class="admin-request-table-inner">
        <tr class="admin-request-table-row">
            <th class="admin-request-table-header">状態</th>
            <th class="admin-request-table-header">名前</th>
            <th class="admin-request-table-header">対象日時</th>
            <th class="admin-request-table-header">申請理由</th>
            <th class="admin-request-table-header">申請日時</th>
            <th class="admin-request-table-header">詳細</th>
        </tr>

        @foreach($requests as $requestItem)
        <tr class="admin-request-table-row">
            <td class="admin-request-table-item">{{ $requestItem->status_label }}</td>
            <td class="admin-request-table-item">{{ $requestItem->user->name ?? '—' }}</td>
            <td class="admin-request-table-item">
                {{ \Carbon\Carbon::parse($requestItem->attendance->work_date ?? null)->format('Y/m/d') ?? '—' }}</td>
            <td class="admin-request-table-item">{{ $requestItem->reason ?? '—' }}</td>
            <td class="admin-request-table-item">
                {{ \Carbon\Carbon::parse($requestItem->created_at)->format('Y/m/d H:i') }}</td>
            <td class="admin-request-table-item">
                <a class="admin-request-table-item-link"
                    href="{{ route('admin.stamp_correction_request.showApprove', $requestItem->attendance_id) }}">詳細</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>

@endsection