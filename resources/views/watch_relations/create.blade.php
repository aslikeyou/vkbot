@extends('watch_relations.layout2')

@section('content')
    @if($error)
        <p>{{$error}}</p>
    @endif

    <form method="post" action="/watch_relations">
        <select name="myGroupId">
            @foreach($myGroups as $myGroup)
                <option value="{{$myGroup->id}}">{{$myGroup->name}}</option>
            @endforeach
        </select>
        <select name="otherGroupId">
            @foreach($otherGroups as $myGroup)
                <option value="{{$myGroup->id}}">{{$myGroup->name}}</option>
            @endforeach
        </select>
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="submit">
    </form>
@endsection