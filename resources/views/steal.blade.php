@extends('layout')

@section('content')
    @if (isset($post_id))
        {{$post_id}} <br>
    @endif

    <form method="post">
        <input name="steal[url_to_steal]" placeholder="url to steal">
        <br>
        <select name="steal[group_id]">
            @foreach($groups as $key => $val)
                <option value="{{$val->id}}">{{$val->name}}</option>
            @endforeach
        </select>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit">
    </form>

@endsection