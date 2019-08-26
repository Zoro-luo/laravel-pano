@extends('layouts.app')

@section('content')
    <div class="container">
      @if(Auth::guest())
        [THIS  PANO  HOME!!] => [AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA]
      @else
        HELLO! &nbsp;<span style="color: palevioletred">{{ Auth::user()->name }}</span>
      @endif
</div>
@endsection
