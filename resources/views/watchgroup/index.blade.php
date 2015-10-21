@extends('watchgroup.layout2')

@section('content')
    List of all existing items: <br>
    @foreach($items as $item)
        <div style="border: 1px solid sienna">
            {{$item->id}}<br>
            {{$item->name}}<br>
            {{$item->screen_name}}<br>
            <form method="post" action="watch_groups/{{$item->id}}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="_method" value="DELETE">
                <input type="submit" value="DELETE '{{$item->screen_name}}'">
            </form>
        </div>
    @endforeach
@endsection