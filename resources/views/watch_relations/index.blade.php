@extends('watch_relations.layout2')

@section('content')
    List of all existing items: <br>
    @foreach($items as $item)
        <div style="border: 1px solid sienna">
            {{$item->id}}<br>
            {{$item->name}}<br>
            {{$item->screen_name}}<br>
            <div>
                @foreach($item->otherGroups as $item2)
                    <ul>
                        <li>
                            {{$item2->id}} | {{$item2->name}} | {{$item2->screen_name}} <br>
                            <form method="post" action="{{\App\Http\Controllers\WatchRelationController::NAME}}/{{$item->id}},{{$item2->id}}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="submit" value="DELETE '{{$item->screen_name}} | {{$item2->screen_name}}'">
                            </form>
                        </li>
                    </ul>
                @endforeach
            </div>
        </div>
    @endforeach
@endsection