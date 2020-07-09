@extends('layout.admin')

@section('contents')
    <p>{{count($users)}}アカウント</p>
    <ul>
        @foreach($users as $user)
            <li>{{$user['name']}} : {{$user['created_at']}}</li>
        @endforeach
    </ul>
@endsection
