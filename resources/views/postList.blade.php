@extends('layout')
@section('title', 'Tweet List')
@section('header')
    @include('header')
@endsection
@section('content')
    <div class="container">
        @if(count($postList))
            <ul class="list-group mt-2" id="postForm">
                @foreach($postList as $post)
                    @if($post->status=='success')
                        <?php
                            $twitterDecode=json_decode($post->twitter_content);
                        ?>
                        <li class="list-group-item itemFile">
                            <p>
                                @if(!empty($twitterDecode->extended_entities->media[0]->media_url))
                                    <img src="{!! $twitterDecode->extended_entities->media[0]->media_url !!}" width="120" class="mr-2">
                                @endif
                                <a href="{!! Twitter::linkTweet($twitterDecode) !!}" target="_blank">{!! $twitterDecode->text !!}</a> <br>
                                <small>{!! Twitter::ago($twitterDecode->created_at) !!}</small>
                            </p>
                            <p><span class="badge badge-success">Status: <span class="">{!! $post->status !!}</span></span><a href="#" class="ml-2 text-danger delPost" data-id="{!! $post->id !!}">Delete</a></p>
                            <small class="text-muted">Task Scheduling: {!! $post->time_option !!}</small>

                        </li>
                    @else
                        <li class="list-group-item itemFile">
                            <p>
                                @if(!empty($post->image))
                                    <img src="{!! $post->image !!}" width="120" class="mr-2">
                                @endif
                                {!! $post->content !!}
                            </p>
                            <p><span class="badge badge-secondary">Status: <span class="">{!! $post->status !!}</span></span><a href="#" class="ml-2 text-danger delPost" data-id="{!! $post->id !!}">Delete</a></p>
                            <small class="text-muted">Task Scheduling: {!! $post->time_option !!}</small>
                        </li>
                    @endif
                @endforeach
            </ul>
        @else
            <div class="alert alert-warning mt-2" role="alert">
                Tweet empty!
            </div>
        @endif
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ URL::asset('gritter/jquery.gritter.css') }}">
@endsection
@section('script')
    <script src="{{ URL::asset('gritter/jquery.gritter.min.js') }}"></script>
    <script>
        $("#postForm").on("click",".delPost",function() {
            if(confirm("Are you sure you want to delete?")){
                $(this).parent().closest(".itemFile").remove();
                var formData = new FormData();
                formData.append("id", $(this).attr('data-id'));
                var xhrRequest = $.ajax({
                    url: "{!! route('tweetDelete') !!}",
                    headers: {"X-CSRF-TOKEN": $("meta[name=_token]").attr("content")},
                    type: "post",
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    dataType:"json",
                    success:function(result) {
                        if(result.success==true){
                            jQuery.gritter.add({
                                title: "Notification!",
                                text: result.message,
                                class_name: "growl-success",
                                sticky: false,
                                time: ""
                            });
                            if($('.itemFile').length<=0){
                                location.reload();
                            }
                        }
                    }
                });
                return false;
            }
        });
    </script>
@endsection