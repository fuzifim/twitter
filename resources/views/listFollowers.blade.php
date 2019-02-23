@extends('layout')
@section('title', 'Follower list')
@section('header')
    @include('header')
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group mt-2">
                    <div class="alert alert-info p-2">
                        <strong>Cung Cấp đến mọi người ⭐ ⭐ ⭐ ⭐ ⭐</strong>
                        <p>Đăng tin lên Cung Cấp để cung cấp sản phẩm, dịch vụ kinh doanh đến mọi người hoàn toàn miễn phí! </p>
                    </div>
                    <div class="btn-group d-flex" role="group"><a class="btn btn-success w-100" href="https://cungcap.net" target="_blank"><h4>Đăng tin miễn phí</h4></a></div>
                </div>
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
                                    <small><code class="text-muted">Id: {!! $follower->twitter_id !!}</code></small>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            <div class="col-md-8">
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
                                    <small><code class="text-muted">Id: {!! $follower->twitter_id !!}</code></small>
                                </li>
                            @endforeach
                        </ul>
                        {{ $listFollowers->links() }}
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