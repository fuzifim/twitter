<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Jobs\ProcessTwitter;
use App\User;
use App\Post;
use Input;
use Session;
use Socialite;
use Auth;
use Twitter;
use Cache;
use File;
use Validator;
use Carbon\Carbon;
use DB;
class IndexController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    //use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = '/';
    public $_user;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        # $this->middleware('guest')->except('logout');
    }
    public function index()
    {
        $listFollowers=DB::table('users')
            //->join('user_follow', 'users.id', '=', 'user_follow.follow_id')
            //->where('user_follow.user_id',$user->id)
            ->select('users.*')
            ->simplePaginate(15);
        return view('listFollowers',array(
            'listFollowers'=>$listFollowers,
        ));
    }

}
