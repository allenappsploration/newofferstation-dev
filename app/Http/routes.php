<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Internal use routes
 */
Route::get('feed', ['as' => 'fetch', 'uses' => 'SocialHub\FeedController@index']);
Route::get('post', ['as' => 'fetch', 'uses' => 'PostController@creation']);

/**
 * Api for outsiders
 */
Route::get('api/posts', 'Api\PostController@index');