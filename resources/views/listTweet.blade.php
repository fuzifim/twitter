@extends('layout')
@section('title', $user->name)
@section('header')
    @include('header')
@endsection
@section('content')
    <div class="container">
        <div class="form-group mt-2">
            <h1><img src="{!!  $user->avatar !!}"><strong class="ml-2">{{$user->name}}</strong></h1>
            {!! Twitter::linkUser($user->nickname) !!}
        </div>
        <div class="row">
            <div class="col-4">
                <div class="form-group mt-2">
                    <div class="alert alert-info p-2">
                        <strong>Cung Cấp đến mọi người ⭐ ⭐ ⭐ ⭐ ⭐</strong>
                        <p>Đăng tin lên Cung Cấp để cung cấp sản phẩm, dịch vụ kinh doanh đến mọi người hoàn toàn miễn phí! </p>
                    </div>
                    <div class="btn-group d-flex" role="group"><a class="btn btn-success w-100" href="https://cungcap.net" target="_blank"><h4>Đăng tin miễn phí</h4></a></div>
                </div>
                <div class="form-group mt-2">
                    <div class="form-group mt-2">
                        @if(count($newUserActive))
                            <ul class="list-group">
                                @foreach($newUserActive as $follower)
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
                    <h3>List Followers</h3>
                    @if(count($listFollowers))
                        <ul class="list-group">
                            @foreach($listFollowers as $follower)
                                <li class="list-group-item">
                                    @if($follower->follow=='success' || $follower->twitter=='success')
                                        <a href="{!! route('user.timeline',$follower->nickname) !!}"><img src="{!! $follower->avatar !!}" width="32" class="mr-2"><strong>{!! $follower->name !!}</strong></a>
                                    @else
                                        <img src="{!! $follower->avatar !!}" width="32" class="mr-2"><strong>{!! $follower->name !!}</strong>
                                    @endif
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