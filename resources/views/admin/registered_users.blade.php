@extends('layout.admin')

@section('contents')
    <div class="justify-between">
        <p>登録ユーザー</p>
        <p>{{$users->firstItem()}}-{{$users->lastItem()}}/{{$users->total()}}</p>
    </div>
    <ul class="list">
        @foreach($users as $user)
            <li class="list__item">{{$user['name']}} : {{$user['created_at']}}</li>
        @endforeach
    </ul>
    {{$users->links()}}
@endsection
