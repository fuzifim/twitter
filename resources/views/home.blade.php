@extends('layout')
@section('title', 'ATC Analyze')
@section('header')
    @include('header')
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
                @if(Auth::check())
                    <?php
                    $user=Auth::user();
                    ?>
                    <div class="card my-5">
                        <div class="card-body">
                            @if(!empty($user->avatar))
                                <div class="form-group">
                                    <img src="{!!  $user->avatar !!}"><strong class="ml-2">{{Auth::user()->name}}</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('css')
@endsection
@section('script')
@endsection