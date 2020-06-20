<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible"
          content="ie=edge">
    <link rel="icon" href="/images/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500;700&display=swap" rel="stylesheet">
    <title>{{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            box-sizing: border-box;
            font-family: sans-serif;
        }
        .header{
            background-color: #3949AB;
            height: 56px;
            padding: 4px 16px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.2),
                        0 4px 5px 0 rgba(0, 0, 0, 0.14),
                        0 1px 10px 0 rgba(0, 0, 0, 0.12);
        }
        .site-title{
            padding-left: 4px;
            font-size: 20px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            letter-spacing: 0.05em;
        }
        .card-wrap{
            padding: 12px;
            max-width: 600px;
            margin: 0 auto;
        }
        .card{
            width: 100%;
            box-shadow: 0 3px 1px -2px rgba(0, 0, 0, 0.2),
                        0 2px 2px 0 rgba(0, 0, 0, 0.14),
                        0 1px 5px 0 rgba(0, 0, 0, 0.12);
            border-radius: 4px;
        }
        .card-inner{
            padding: 16px;
        }
        .card-title{
            font-size: 20px;
            color: rgba(0, 0, 0, 0.87);
        }
        .card-text{
            margin: 16px 0;
            font-size: 14px;
            font-weight: 300;
            color: rgba(0, 0, 0, 0.6);
        }
        .card-link{
            font-size: 14px;
            font-weight: 300;
            color: #1976d2;
        }
        @media screen and (min-width: 600px){
            .card-wrap{
                margin-top: 48px;
            }
        }
        @media screen and (min-width: 960px){
            .header{
                height: 64px;
            }
            .site-title{
                margin-left: 20px;
            }
        }
    </style>
</head>
<body>
<header class="header">
    <a href="/" class="site-title">ShareCalendar</a>
</header>
<main>

    <div class="card-wrap">
        <div class="card">
            <div class="card-inner">
                <p class="card-title">500</p>
                <p class="card-text">サーバーで問題が発生したためアクセスできませんでした。</p>
                <a href="/" class="card-link">TOPページ</a>
            </div>
        </div>
    </div>
</main>
</body>
</html>
