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
    public function index(Request $request)
    {
        $page = $request->has('page') ? $request->query('page') : 1;
        $listFollowers = Cache::store('memcached')->remember('home_list_follow_'.$page,1, function()
        {
            return DB::table('users')
                ->select('users.*')
                ->simplePaginate(15);
        });
        $newUserActive = Cache::store('memcached')->remember('new_user_active',1, function()
        {
            return DB::table('users')
                ->where('follow','success')
                ->orWhere('twitter','success')
                ->select('users.*')
                ->orderBy('updated_at','desc')
                ->take(10)->get();
        });
        return view('listFollowers',array(
            'listFollowers'=>$listFollowers,
            'newUserActive'=>$newUserActive
        ));
    }

}
