@extends('layout.admin')

@section('contents')
    <p>{{count($users)}}アカウント</p>
    <ul class="list">
        @foreach($users as $user)
            <li class="list__item">{{$user['name']}} : {{$user['created_at']}}</li>
        @endforeach
    </ul>
@endsection
