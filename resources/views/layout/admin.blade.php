<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <link rel="icon" href="{{asset('/favicon.ico')}}">
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <title>{{ config('app.name') }}</title>
</head>
<body>
<div class="container">
    <nav class="nav">
        <form action="{{route('admin_logout')}}" method="post">
            @csrf
            <button type="submit">ログアウト</button>
        </form>
    </nav>
    <main class="contents">
        @yield('contents')
    </main>
</div>
</body>
</html>
