{{-- resources/views/auth/verify-email.blade.php --}}
@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/email.css') }}">
@endsection

@section('content')
<div class="verify-wrapper">
    <p class="verify-text">
        登録していただいたメールアドレスに認証メールを送付しました。
        メール認証を完了してください。
    </p>

    {{-- 案内用ボタン --}}
    <div class="primary-action">
        <a href="{{ route('verification.notice') }}" class="primary-button">
            認証はこちらから
        </a>
    </div>

    @if (session('status') === 'verification-link-sent')
        <p class="resent-message">認証メールを再送しました。</p>
    @endif

    {{-- 再送リンク --}}
    <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
        @csrf
        <button type="submit" class="resend-link">
            認証メールを再送する
        </button>
    </form>
</div>
@endsection