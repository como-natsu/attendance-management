<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>attendance-management</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css')}}">
    <link rel="stylesheet" href="{{ asset('css/auth.css')}}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header-inner">
            <img class="header-logo-img" src="{{ asset('storage/image/CoachTech_White.png') }}" alt="coachtech-logo">
        </div>
    </header>

    <div class="auth-content">
        @yield('content')
    </div>
</body>

</html>