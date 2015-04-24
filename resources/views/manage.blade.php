@extends('app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <h1>Write a New Post</h1>
                <hr>

                {!! Form::open(array('url' => 'foo/bar', 'files' => true)) !!}
                    {!!Form::label('datetime', 'Select date')!!}
                    {!! Form::text('datetime') !!}
                    <br>
                    <br>
                    {!! Form::file('image1') !!}
                    {!! Form::file('image2') !!}
                    {!! Form::file('image3') !!}
                    {!! Form::file('image4') !!}
                    {!! Form::file('image5') !!}
                    <br>
                    {!! Form::submit('Click Me!'); !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection