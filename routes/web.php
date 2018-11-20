<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', ['as' => 'home', function () {

    return view('home');
}]);
Route::get('/search', ['as' => 'search', function () {
    dd(json_decode(Twitter::getSearch(['screen_name'=>'conduongviet','q'=>'%23Beauty','format'=>'json'])));
}]);
Route::get('twitter/login', ['as' => 'twitter.login',
    'uses' => 'Auth\LoginController@redirectToProvider']);
Route::get('timeLine/{nickname}', ['as' => 'user.timeline',
    'uses' => 'Auth\LoginController@getUserTimeLine']);
Route::get('twitter/callback', ['as' => 'twitter.callback',
    'uses' => 'Auth\LoginController@handleProviderCallback']);
Route::get('tweet', ['as' => 'tweet',
    'uses' => 'Auth\LoginController@tweet']);
Route::post('tweet', ['as' => 'tweet.request',
    'uses' => 'Auth\LoginController@tweetRequest']);
Route::get('followers', ['as' => 'followers',
    'uses' => 'Auth\LoginController@followers']);
Route::get('tweetList', ['as' => 'tweetList',
    'uses' => 'Auth\LoginController@tweetList']);
Route::post('tweetDelete', ['as' => 'tweetDelete',
    'uses' => 'Auth\LoginController@postDelete']);
Route::get('post', ['as' => 'post',
    'uses' => 'Auth\LoginController@post']);
Route::post('uploadImage', ['as' => 'uploadImage',
    'uses' => 'Auth\LoginController@uploadImage']);
Route::post('deleteImage', ['as' => 'deleteImage',
    'uses' => 'Auth\LoginController@deleteImage']);
Route::get('logout', ['as' => 'logout',
    'uses' => 'Auth\LoginController@logout']);