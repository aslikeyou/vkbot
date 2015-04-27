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

Route::get('/manage', function() {
    return view('manage');
});

// https://github.com/vladkens/VK/blob/master/Samples/example-2.php
Route::get('/manage1', function() {
    $vk = new VK\VK(Config::get('vk.app_id'), Config::get('vk.api_secret'), Config::get('vk.access_token'));

    if(!$vk->isAuth()) {
        return redirect('/');
    }

    $res = VkApiHelper::uploadAudio($vk,
        storage_path().'/app/processed/16_14.05/2844479654ee.mp3'
    );


    echo "<pre>";
    var_dump($res);

});

Route::match(['get', 'post'],'/', function() {
    $vk_config = array(
        'app_id'        => '4865330',
        'api_secret'    => 'L8lsIl6koOlf3aM4Lh5j',
        //'callback_url'  => 'http://localhost:8000',
        'api_settings'  => 'wall,friends,offline, photos, audio' // In this example use 'friends'.
        // If you need infinite token use key 'offline'.
    );
    $vk = new VK\VK($vk_config['app_id'], $vk_config['api_secret']);

    if(isset($_POST['code'])) {
        file_put_contents('access_token.txt', $_POST['code']);
        return redirect('/manage');
    }

    $url = $vk->getAuthorizeUrl($vk_config['api_settings']);
    $url = str_replace('response_type=code', 'response_type=token', $url);

    return view('vkauth', ['authUrl' => $url]);
});



//Route::get('/', 'WelcomeController@index');
//
//Route::get('home', 'HomeController@index');
//
//Route::controllers([
//	'auth' => 'Auth\AuthController',
//	'password' => 'Auth\PasswordController',
//]);
