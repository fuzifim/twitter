<?php

namespace App\Http\Controllers\Auth;

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
class LoginController extends Controller
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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
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
    public function redirectToProvider()
    {
        return Socialite::driver('twitter')->redirect();
    }
    public function handleProviderCallback(User $user)
    {
        $socialize_user = Socialite::driver('twitter')->user();
        $user=User::where('twitter_id', $socialize_user->getId())->first();
        if($user){
            $user->oauth_token=$socialize_user->token;
            $user->oauth_token_secret=$socialize_user->tokenSecret;
            $user->save();
            Auth::login($user);
            return redirect('/');
        }
        $user = new User;
        $user->name=$socialize_user->name;
        $user->nickname=$socialize_user->nickname;
        $user->email=$socialize_user->email;
        $user->avatar=$socialize_user->avatar;
        $user->avatar_original=$socialize_user->avatar_original;
        if(!empty($socialize_user->user['profile_banner_url'])){
            $user->banner=$socialize_user->user['profile_banner_url'];
        }
        $user->twitter_id = $socialize_user->getId();
        $user->oauth_token=$socialize_user->token;
        $user->oauth_token_secret=$socialize_user->tokenSecret;
        $user->follow='pending';
        $user->save();
        Auth::login($user);
        return redirect('/');
    }
    public function followers(){
        if(Auth::check()){
            $user=Auth::user();
            if(!empty($user->oauth_token) && !empty($user->oauth_token_secret)){
                $this->_user=$user;
                $this->addFollow();
                $listFollowers=DB::table('users')
                    ->join('user_follow', 'users.id', '=', 'user_follow.follow_id')
                    ->where('user_follow.user_id',$user->id)
                    ->select('users.*')
                    ->simplePaginate(15);
                return view('listFollowers',array(
                    'listFollowers'=>$listFollowers,
                ));
            }
        }else{
            return redirect('/');
        }
    }
    public function autoAddFollow(){
        $getUser=User::where('follow','pending')
            ->where('twitter','=',NULL)
            ->limit(1)->get();
        //ProcessTwitter::dispatch($getUser)->delay(Carbon::now()->addMinutes(3));
        $job = (new ProcessTwitter($getUser))
            ->delay(Carbon::now()->addMinutes(3));

        dispatch($job);
    }
    public function addFollow(){
        if($this->_user->follow=='pending'){
            $user=$this->_user;
            try {
                $getFollow=Twitter::getFollowers(['screen_name' => $user->nickname,'count' => 20, 'format' => 'json']);
                $listFollow=json_decode($getFollow);
                if(!empty($listFollow->users) && count($listFollow->users)){
                    foreach($listFollow->users as $follow){
                        $userFollow=User::where('twitter_id',$follow->id)->first();
                        if(empty($userFollow->id)){
                            $userFollow = new User;
                            $userFollow->name=$follow->name;
                            $userFollow->nickname=$follow->screen_name;
                            if(!empty($follow->profile_image_url)){
                                $userFollow->avatar=$follow->profile_image_url;
                            }
                            if(!empty($follow->profile_banner_url)){
                                $userFollow->banner=$follow->profile_banner_url;
                            }
                            $userFollow->twitter_id = $follow->id;
                            $userFollow->follow='pending';
                            $userFollow->save();
                        }
                        DB::table('user_follow')
                            ->where('user_id',$user->id)
                            ->where('follow_id',$userFollow->id)
                            ->delete();
                        DB::table('user_follow')->insert(
                            ['user_id' => $user->id, 'follow_id' => $userFollow->id]
                        );
                    }
                    $user->follow='success';
                    $user->save();
                    return 'Follow success, ';
                }
            }
            catch (\Exception $e) {
                $user->follow='faild';
                $user->save();
                return 'Follow faild, ';
            }
        }
    }
    public function getUserTimeLine(Request $request){
        $user=User::where('nickname',$request->route('nickname'))->first();
        $this->_user=$user;
        //$this->addFollow();
        //$this->addTimeLine();
        $listTweet=DB::table('tweet')
            ->where('user_id',$user->id)
            ->simplePaginate(10);
        $listFollowers=DB::table('users')
            ->join('user_follow', 'users.id', '=', 'user_follow.follow_id')
            ->where('user_follow.user_id',$user->id)
            ->select('users.*')
            ->get();
        return view('listTweet',array(
            'listTweet'=>$listTweet,
            'listFollowers'=>$listFollowers,
            'user'=>$user
        ));
    }
    public function addTimeLine(){
        $user=$this->_user;
        if(!empty($user->id) && empty($user->twitter)){
            try {
                $listTweet=Twitter::getUserTimeline(['screen_name' => $user->nickname, 'count' => 20, 'format' => 'json']);
                $tweetDecode=json_decode($listTweet);
                if(count($tweetDecode)){
                    foreach($tweetDecode as $tweet){
                        $getTweet=DB::table('tweet') ->where('tweet_id',$tweet->id)->first();
                        if(empty($getTweet->id)){
                            $getTweetId=DB::table('tweet')->insertGetId(
                                [
                                    'user_id' => $user->id,
                                    'tweet_created_at' => $tweet->created_at,
                                    'tweet_id'=>$tweet->id,
                                    'text'=>$tweet->text
                                ]
                            );
                        }else{
                            $getTweetId=$getTweet->id;
                        }
                        if(!empty($tweet->entities->hashtags) && count($tweet->entities->hashtags)){
                            foreach($tweet->entities->hashtags as $hashtag){
                                $getHashtag=DB::table('tags') ->where('text',$hashtag->text)->first();
                                if(empty($getHashtag->id)){
                                    $getHashtagId=DB::table('tags')->insertGetId(
                                        [
                                            'text' => $hashtag->text
                                        ]
                                    );
                                }else{
                                    $getHashtagId=$getHashtag->id;
                                }
                                DB::table('tags_relation_tweet')
                                    ->where('tweet_id',$getTweetId)
                                    ->where('tag_id',$getHashtagId)
                                    ->delete();
                                DB::table('tags_relation_tweet')->insert(
                                    ['tweet_id' => $getTweetId, 'tag_id' => $getHashtagId]
                                );
                            }
                        }
                    }
                }
                $user->twitter='success';
                $user->save();
                return 'Twitter success, ';
            }
            catch (\Exception $e) {
                $user->twitter='faild';
                $user->save();
                return 'Twitter faild, ';
            }
        }
    }
    public function tweetList(){
        $postList=Post::orderBy('updated_at','desc')->simplePaginate(10);
        return view('postList',array(
            'postList'=>$postList,
        ));
    }
    public function tweet(){
        return view('tweet');
    }
    public function tweetRequest(Request $request){
        if(Auth::check()){
            $user=Auth::user();
            $messages = array();
            $rules = array(
                'image' => 'required',
                'content'=>'required|min:6',
                'timeOption'=>'required',
            );
            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails())
            {
                return response()->json(['status'=>false,
                    'error'=>$validator->errors(),
                    'message'=>'Error! '.$validator->errors()->first()
                ]);
            }else{
                $post=new Post();
                $post->user_id=$user->id;
                $post->content=$request->content;
                $post->image=$request->image;
                $post->time_option=Carbon::parse($request->timeOption)->format('Y-m-d H:i:s');
                $post->status='pending';
                $post->save();
                Session::forget('image_tmp');
                Session::forget('image_path');
                return response()->json(['success'=>true,
                    'message'=>'Save new tweet success! '
                ]);
            }
        }
    }
    public function changeStatus(){
        $post=Post::where('id',1)->first();
        $post->status='cron';
        $post->save();
    }
    public function post(){
        $posts=Post::where('status','pending')->limit(1)->get();
        foreach($posts as $post){
            if(Carbon::parse($post->time_option)->format('Y-m-d H:i:s') <= Carbon::now()->format('Y-m-d H:i:s')) {
                $user=User::find($post->user_id);
                if(!empty($user->oauth_token) && !empty($user->oauth_token_secret)){
                    $request_token = [
                        'token'  => $user->oauth_token,
                        'secret' => $user->oauth_token_secret,
                    ];
                    Twitter::reconfig($request_token);
                    $credentials = Twitter::getCredentials();
                    if (is_object($credentials) && !isset($credentials->error))
                    {
                        $uploaded_media = Twitter::uploadMedia(['media' => File::get(public_path($post->image))]);
                        $postTweet=Twitter::postTweet(['status' => $post->content, 'media_ids' => $uploaded_media->media_id_string]);
                        $post->status='success';
                        $post->twitter_content=json_encode($postTweet);
                        $post->save();
                        File::delete(public_path($post->image));
                        return response()->json(['success'=>true,
                            'message'=>$postTweet
                        ]);
                    }
                }
            }
        }
    }
    public function postDelete(Request $request){
        $post=Post::find($request->id);
        if(!empty($post->id)){
            if(!empty($post->image)){
                File::delete(public_path($post->image));
            }
            $post->delete();
            return response()->json(['success'=>true,
                'message'=>'delete tweet success! '
            ]);
        }else{
            return response()->json(['success'=>false,
                'message'=>'Delete faild! '
            ]);
        }
    }
    public function deleteImage(){
        $image = Input::get('image');
        $image_path = Input::get('dataPath');
        File::delete($image_path);
        Session::forget('image_tmp');
        Session::forget('image_path');
        return response()->json(['success'=>true,
            'message'=>'Deleted success! '
        ]);
    }
    public function uploadImage(){
        $fileupload = Input::file('file');
        $mime = $fileupload->getMimeType();
        $file_size = $fileupload->getSize();
        $name=preg_replace('/\..+$/', '', $fileupload->getClientOriginalName());
        $filename=str_random(5).'-'.Str::slug($name).'.'.$fileupload->getClientOriginalExtension();
        $path = public_path(). '/media/tmp/';
        $fileupload->move($path,$filename);
        $file_path = $path.$filename;
        Session::put('image_tmp', $filename);
        Session::put('image_path', $file_path);
        return response()->json(['success'=>true,
            'message'=>'upload image success',
            'image_path'=>$file_path,
            'file_name'=>$filename
        ]);

    }
    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
