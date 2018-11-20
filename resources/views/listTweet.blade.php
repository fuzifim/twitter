@extends('layout')
@section('title', $user->name)
@section('header')
    @include('header')
@endsection
@section('content')
    <div class="container">
        <div class="form-group mt-2">
            <h1><img src="{!!  $user->avatar !!}"><strong class="ml-2"><a href="{!! Twitter::linkUser($user->nickname) !!}" target="_blank">{{$user->name}}</a></strong></h1>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="form-group mt-2">
                    <h3>List Followers</h3>
                    @if(count($listFollowers))
                        <ul class="list-group">
                            @foreach($listFollowers as $follower)
                                <li class="list-group-item">
                                    <a href="{!! route('user.timeline',$follower->nickname) !!}"><img src="{!! $follower->avatar !!}" width="32" class="mr-2"><strong>{!! $follower->name !!}</strong></a>
                                    <a href="{!! Twitter::linkUser($follower->nickname) !!}" class="text-muted" target="_blank"><small>link tweet</small></a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            <div class="col-8">
                <div class="form-group mt-2">
                    <h3>List Tweet</h3>
                    @if(count($listTweet))
                        <ul class="list-group">
                            @foreach($listTweet as $tweet)
                                <li class="list-group-item">
                                    {!! $tweet->text !!}
                                    <p><small class="text-muted">{!! Twitter::ago($tweet->tweet_created_at) !!}</small></p>
                                </li>
                            @endforeach
                        </ul>
                        <div class="form-group mt-2">
                            {{ $listTweet->links() }}
                        </div>
                    @else
                        <div class="alert alert-warning mt-2" role="alert">
                            Tweet empty!
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
@endsection
@section('script')
@endsection