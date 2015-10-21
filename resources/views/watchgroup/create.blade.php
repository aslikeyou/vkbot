@extends('watchgroup.layout2')

@section('content')
    @if($error)
        <p>{{$error}}</p>
    @endif

    <form method="post" action="/watch_groups">
        <input type="text" name="id" placeholder="name or id">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit">
    </form>
@endsection