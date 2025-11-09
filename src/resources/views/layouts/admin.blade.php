<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>attendance-management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{ asset('css/common.css')}}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="container">
            <div class="header-inner">
                <img class="header-logo-img" src="{{ asset('storage/image/CoachTech_White.png') }}"
                    alt="coachtech-logo">
                <div class="header-list">
                    <nav class="header-nav">
                        <ul class="header-nav-list">
                            <li class="header-nav-item">
                                <a class="header-nav-link" href="/admin/attendance/list">勤怠一覧</a>
                            </li>
                            <li class="header-nav-item">
                                <a class="header-nav-link" href="/admin/staff/list">スタッフ一覧</a>
                            </li>
                            <li class="header-nav-item">
                                <a class="header-nav-link" href="/stamp_correction_request/list">申請一覧</a>
                            </li>
                            <li class="header-nav-item">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="header-nav-button">ログアウト</button>
                                </form>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <div class="content">
        @yield('content')
    </div>

</body>

</html>