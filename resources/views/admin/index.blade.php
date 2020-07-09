@extends('layout.admin')

@section('contents')
    @foreach($loginHistories as $loginHistory)
        <p>{{$loginHistory['created_at']}} : {{$loginHistory['ip']}}</p>
    @endforeach
@endsection