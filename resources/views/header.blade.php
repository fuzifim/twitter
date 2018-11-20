<nav class="navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand" href="{!! route('home') !!}">ATC Analyze</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample02" aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExample02">
        <ul class="navbar-nav mr-auto">
            @if(Auth::check())
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('followers') !!}">Follower list</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('tweet') !!}">New tweet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{!! route('tweetList') !!}">Tweet list</a>
                </li>
            @endif
        </ul>
        <ul class="navbar-nav ml-auto">
            @if(Auth::check())
                <li class="nav-item active">
                    <a class="nav-link" href="{!! route('home') !!}"><img src="{!! Auth::user()->avatar !!}" width="20" class="mr-2">{!! Auth::user()->name !!}</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="{!! route('logout') !!}">Logout</a>
                </li>
            @else
                <li class="nav-item active">
                    <a class="nav-link" href="{!! route('twitter.login') !!}">Twitter login <span class="sr-only">(current)</span></a>
                </li>
            @endif
        </ul>
    </div>
</nav>
<div class="container">
    <form class="form-group" id="searchform" action="# method="get"><div class="card-body row no-gutters align-items-center"><div class="col"><input class="form-control form-control-lg form-control-borderless" name="v" id="searchAll" type="text" placeholder="Enter keyword... " value="" autocomplete="off"><input type="hidden" name="t" id="searchType" value=""><input type="hidden" name="i" id="searchId" value=""></div><div class="col-auto"><button class="btn btn-lg btn-success" type="submit">Search</button></div></div></form>
</div>