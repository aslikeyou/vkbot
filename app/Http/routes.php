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
use Illuminate\Support\Debug\Dumper;
Route::get('/test', 'WelcomeController@test');
Route::resource('watch_groups', 'WatchGroupController');
Route::resource('watch_relations', 'WatchRelationController');
Route::match(['get', 'post'], '/', 'WelcomeController@index');
Route::match(['get'], '/wall', 'WelcomeController@getPost');
Route::match(['get'], '/welcome', 'WelcomeController@welcome');
Route::match(['get', 'post'], '/steal', 'WelcomeController@steal');
Route::match(['get', 'post'], '/watch', 'WelcomeController@watch');
Route::match(['get'], '/mygroups', 'WelcomeController@setUpMyGroups');
Route::match(['get', 'post'], '/sqlparse', 'WelcomeController@sqlparse');



//Route::get('/manage', function() {
//    return view('manage');
//});

//Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
//
//Route::get('/groups/{uid}/last', function($uid) {
//    $vk = VkApiHelper::getI();
//
//    $res = $vk->api('newsfeed.get', [
//        'source_ids' => $uid,
//        'filter'   => 'post',
//        'count'    => 2,
//    ]);
//    if(!isset($res['response'])) {
//        throw new \App\Exceptions\BadResponseException(
//            "Vk returns wrong response",
//            $res
//        );
//    }
//    $res = $res['response']['items'][1];
//
//    $newres = $vk->api('wall.post', [
//        'owner_id' => '-97448590',
//        'message' => $res['text'],
//        'attachments' => implode(',',array_map(function($item) {
//            $media = $item[$item['type']];
//
//            $str = $item['type'].$media['owner_id'].'_'.$media['pid'];
//            echo $str.'<br>';
//            return $str;
//        }, $res['attachments']))
//    ],'array', 'post');
//    echo "<pre>";
//    print_r($newres);
//});



//Route::get('/groups/delete/smaller_then/{count}', function($count = 50000) {
//    $vk = new VK\VK(Config::get('vk.app_id'), Config::get('vk.api_secret'), Config::get('vk.access_token'));
//    $res = $vk->api('groups.get', [
//        'extended' => 1
//    ]);
//    if(!isset($res['response'])) {
//        throw new Exception('No response');
//    }
//    $res = $res['response'];
//    if(!is_array($res) || !is_int($res[0])) {
//        throw new Exception('Invalid response');
//    }
//    unset($res[0]);
//    $deletedGroupsCount = 0;
//    foreach($res as $r) {
//        if($r['is_admin'] === 1) {
//            continue ;
//        }
//        $res = $vk->api('groups.getMembers', [
//            'group_id' => $r['gid'],
//            'count' => 0
//        ]);
//        if(!isset($res['response']['count'])) {
//            throw new Exception(print_r($res, 1));
//        }
//
//        if($res['response']['count'] < $count) {
//            $res = $vk->api('groups.leave', [
//                'group_id' => $r['gid'],
//            ]);
//            if(!isset($res['response']) || $res['response'] !== 1) {
//                throw new Exception(print_r($res, 1));
//            }
//            $deletedGroupsCount++;
//        }
//        //break;
//        usleep(1000000);
//    }
//
//    echo "It was deleted #".$deletedGroupsCount.' groups!';
//
//
//});
//
//// https://github.com/vladkens/VK/blob/master/Samples/example-2.php
//Route::get('/manage1', function() {
//    $vk = new VK\VK(Config::get('vk.app_id'), Config::get('vk.api_secret'), Config::get('vk.access_token'));
//
//    if(!$vk->isAuth()) {
//        return redirect('/');
//    }
//
//    $res = VkApiHelper::uploadAudio($vk,
//        storage_path().'/app/processed/16_14.05/2844479654ee.mp3'
//    );
//
//
//    echo "<pre>";
//    var_dump($res);
//
//});

//Route::match(['get', 'post'],'/', function() {
//    $vk_config = array(
//        'app_id'        => '4865330',
//        'api_secret'    => 'L8lsIl6koOlf3aM4Lh5j',
//        //'callback_url'  => 'http://localhost:8000',
//        'api_settings'  => 'wall,friends,offline, photos, audio' // In this example use 'friends'.
//        // If you need infinite token use key 'offline'.
//    );
//    $vk = new VK\VK($vk_config['app_id'], $vk_config['api_secret']);
//
//    if(isset($_POST['code'])) {
//        file_put_contents('access_token.txt', $_POST['code']);
//        return redirect('/manage');
//    }
//
//    $url = $vk->getAuthorizeUrl($vk_config['api_settings']);
//    $url = str_replace('response_type=code', 'response_type=token', $url);
//
//    return view('vkauth', ['authUrl' => $url]);
//});



//Route::get('/', 'WelcomeController@index');
//
//Route::get('home', 'HomeController@index');
//
//Route::controllers([
//	'auth' => 'Auth\AuthController',
//	'password' => 'Auth\PasswordController',
//]);
