<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <meta name="robots" content="noindex,nofollow">
    <link rel="icon" href="{{asset('/favicon.ico')}}">
    <title>{{ config('app.name') }}</title>
</head>
<body>
<form action="{{route('admin_login')}}" method="post">
    @csrf
    <label>
        id:<input type="text" name="name">
    </label>
    <label>
        pass:<input type="text" name="password">
    </label>
    <button type="submit">ログイン</button>
</form>
</body>
</html>
