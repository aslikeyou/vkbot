@extends('layout')

@section('content')
    <form method="post">
        <input type="text" name="watch[other_group]" placeholder="add other group">
        <br>
        <select name="watch[my_group_id]">
            @foreach($myGroups as $key => $val)
                <option value="{{$val->id}}">{{$val->name}}</option>
            @endforeach
        </select>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <br>
        <input type="submit" value="Watch group">
    </form>

@endsection