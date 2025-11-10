@extends('layouts.admin')

@section('css')
<link rel="stylesheet" href="{{ asset('css/staff-list.css') }}">
@endsection

@section('content')
<div class="admin-staff-list">
    <div class="admin-staff-title">
        <img class="line-image" src="{{ asset('storage/image/Line.png') }}" alt="Line-image">
        <p class="staff-title">スタッフ一覧</p>
    </div>

    <table class="staff-table-inner">
        <tr class="staff-table-row">
            <th class="staff-table-header">名前</th>
            <th class="staff-table-header">メールアドレス</th>
            <th class="staff-table-header">月次勤怠</th>
        </tr>

        @foreach($staffs as $staff)
        <tr class="staff-table-row">
            <td class="staff-table-item">{{ $staff->name ?? '' }}</td>
            <td class="staff-table-item">{{ $staff->email ?? '' }}</td>
            <td class="staff-table-item">
                <a class="staff-table-item-link"
                    href="{{ route('admin.staff.attendance', ['id' => $staff->id]) }}">詳細</a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection