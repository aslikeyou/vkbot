@extends('layout')

@section('subnav')
    <ul>
        <li>
            <a href="/{{\App\Http\Controllers\WatchRelationController::NAME}}/">Main</a>
        </li>
        <li>
            <a href="/{{\App\Http\Controllers\WatchRelationController::NAME}}/create">Create123</a>
        </li>
    </ul>
@endsection