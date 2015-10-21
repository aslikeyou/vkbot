@extends('layout')

@section('content')

<a href='javascript:void(0);' target='_top' onClick=window.open("{{ $url }}","Ratting","width=550,height=170,0,status=0,");>
    LINK
</a>

<form method="post">
    <input type="text" name="token" value="" placeholder="token">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="submit">
</form>

@endsection