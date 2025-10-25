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
                <div class="header-right">
                    <form class="header-search" action="/" method="GET">
                        <input class="header-search-input" type="text" name="search" placeholder="なにをお探しですか？"
                            value="{{ request('search') }}">
                        <button class="header-search-button" type="submit">検索</button>
                    </form>
                    <nav class="header-nav">
                        <ul class="header-nav-list">
                            @guest
                            <li class="header-nav-item">
                                <a class="header-nav-link" href="/login">ログイン</a>
                            </li>
                            @else
                            <li class="header-nav-item">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="header-nav-button">ログアウト</button>
                                </form>
                            </li>
                            @endguest
                            <li class="header-nav-item">
                                <a class="header-nav-link" href="/mypage">マイページ</a>
                            </li>
                            <li class="header-nav-item">
                                <a class="header-nav-link header-nav-link--sell" href="/sell">出品</a>
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