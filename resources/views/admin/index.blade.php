@extends('layout.admin')

@section('contents')
    <p>ログイン履歴</p>
    <ul class="list">
    @foreach($loginHistories as $loginHistory)
        <li class="list__item">{{$loginHistory['created_at']}} : {{$loginHistory['ip']}}</li>
    @endforeach
    </ul>
@endsection