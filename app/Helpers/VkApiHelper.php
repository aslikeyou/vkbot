<?php

use VK\VK;
use GuzzleHttp\Client;

class VkApiHelper {
    public static function getI() {
        $vk = new VK(config('vk.app_id'), config('vk.api_secret'), config('vk.access_token'));

        if(!$vk->isAuth()) {
            throw new \Exception('Invalid auth');
        }

        return $vk;
    }

    public static function getCurrentGroup() {
        $a =  config('vk.group_id');
        return $a;
    }

    public static function getA() {
        $vk = \VkApiHelper::getI();
        $res = $vk->api('groups.getMembers', [
            'group_id' => \VkApiHelper::getCurrentGroup(),
            'offset' => '0',
            'count' => '1000'
        ]);

        //if(isset)



    }

    public static function uploadPhoto(VK $vk, $groupId, $files) {
        $user_friends = $vk->api('photos.getWallUploadServer', [
            'group_id' => $groupId
        ]);

        $client = new Client();
        $body = [];
        foreach($files as $f) {
            $body['file' . (count($body) + 1)] = fopen($f, 'r');
        }
        $res = $client->post($user_friends['response']['upload_url'], [
            'body' => $body
        ]);


        $res2 = $vk->api('photos.saveWallPhoto', array_merge([
            'group_id' => $groupId,
        ], $res->json()));

        return $res2['response'];
    }

    public static function uploadAudio(VK $vk, $file) {
        $user_friends = $vk->api('audio.getUploadServer');

        $client = new Client();
        $body = [];

        $body['file'] = fopen($file, 'r');

        $res = $client->post($user_friends['response']['upload_url'], [
            'body' => $body
        ]);

        $b = $vk->api('audio.save', $res->json());

        return $b;
    }
}