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



Route::group([
    'middleware' => 'auth.veryBasic'
], function() {
    Route::get('/logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
    Route::get('/test', 'WelcomeController@test');
    Route::get('/clear-token', 'WelcomeController@clearToken');
    Route::resource('watch_groups', 'WatchGroupController');
    Route::resource('watch_relations', 'WatchRelationController');
    Route::match(['get', 'post'], '/', 'WelcomeController@index');
    Route::match(['get'], '/wall', 'WelcomeController@getPost');
    Route::match(['get'], '/welcome', 'WelcomeController@welcome');
    Route::match(['get', 'post'], '/steal', 'WelcomeController@steal');
    Route::match(['get', 'post'], '/watch', 'WelcomeController@watch');
    Route::match(['get'], '/mygroups', 'WelcomeController@setUpMyGroups');
    Route::match(['get', 'post'], '/sqlparse', 'WelcomeController@sqlparse');
});


