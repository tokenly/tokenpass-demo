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


Route::get('/account', array('as' => 'account.home', 'uses' => 'AccountController@home'));
Route::get('/account/login', array('as' => 'account.login', 'uses' => 'AccountController@login'));
Route::get('/account/logout', array('as' => 'account.logout', 'uses' => 'AccountController@logout'));
Route::get('/account/authorize/callback', array('as' => 'account.auth.callback', 'uses' => 'AccountController@handleTokenpassCallback'));

