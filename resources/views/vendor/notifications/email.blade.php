@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
# {{ $greeting }}
@else
@if ($level === 'error')
# @lang('Whoops!')
@else
{{--# @lang('Hello!')--}}
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}

@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
        case 'error':
            $color = $level;
            break;
        default:
            $color = 'primary';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])
{{ $actionText }}
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
{{ $salutation }}
@else
{{--@lang('Regards'),<br>--}}
{{ config('app.name') }}
@endif

{{-- Subcopy --}}
@isset($actionText)
@slot('subcopy')
{{--    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".--}}
{{--    'into your web browser: [:displayableActionUrl](:actionURL)',--}}
@lang(
	":actionTextボタンが使用できない場合は以下のURLにアクセスしてパスワードを再設定してください。
	[:actionURL](:actionURL)",
        [
            'actionText' => $actionText,
            'actionURL' => $actionUrl,
            'displayableActionUrl' => $displayableActionUrl,
        ]
    )
@endslot
@endisset
@endcomponent
