@extends('layout')

@section('content')
    @foreach($groups as $key => $val)
        <div style="border: 1px solid #888888">
            {{$val->id}} <br>
            {{$val->name}} <br>
            {{$val->screen_name}}
        </div>
    @endforeach
@endsection