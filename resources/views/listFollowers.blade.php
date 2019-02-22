@extends('layout')
@section('title', 'Follower list')
@section('header')
    @include('header')
@endsection
@section('content')
    <div class="container">
        <div class="form-group mt-2">
            @if(count($listFollowers))
                <ul class="list-group">
                    @foreach($listFollowers as $follower)
                        <li class="list-group-item">
                            @if($follower->follow=='success' || $follower->twitter=='success')
                                <a href="{!! route('user.timeline',$follower->nickname) !!}"><img src="{!! $follower->avatar !!}" width="32" class="mr-2"><strong>{!! $follower->name !!}</strong></a>
                            @else
                                <img src="{!! $follower->avatar !!}" width="32" class="mr-2"><strong>{!! $follower->name !!}</strong>
                            @endif
                            <small><code>Id: {!! $follower->twitter_id !!}</code></small>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection
@section('css')
@endsection
@section('script')
@endsection