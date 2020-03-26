<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <script src="https://kit.fontawesome.com/dee60321f1.js" crossorigin="anonymous"></script>
    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body>
<div id="app"></div>
</body>
</html>
