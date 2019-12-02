@extends('template')

@section('content')

    <div>
        @include('header')

            <h2>{{ $title }}</h2>

            <div>
                {{ $content }}
            </div>
        @include('footer')
    </div>

@endsection