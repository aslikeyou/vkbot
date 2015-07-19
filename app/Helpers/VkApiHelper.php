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
    }

    public static function uploadPhoto(VK $vk, $groupId, $files) {
        $user_friends = $vk->api('photos.getWallUploadServer', [
            'group_id' => $groupId,
        ]);

        if(!isset($user_friends['response']['upload_url'])) {
            throw new \App\Exceptions\ArrException(
                        "Vk returned invalid Request"
                        , 1
                        , null
                        , [
                            'params' => [
                                'group_id' => $groupId
                            ],
                            'response' => $user_friends
                        ]);
            
        }

        $client = new Client();
        $body = [];
        foreach($files as $f) {
            $body['file' . (count($body) + 1)] = fopen($f, 'r');
        }

        $res = $client->post($user_friends['response']['upload_url'], [
            'body' => $body
        ]);

        if ($res->getStatusCode() !== 200) {
            throw new \App\Exceptions\ArrException(
                "Can not upload photo files"
                , 1
                , null
                , [
                    'params' => [
                        'upload_url' => $user_friends['response']['upload_url']
                        ,'body' => $body
                    ],
                    'response' => $res->getBody()
                ]
                );
        }
        
        $paramsSaveWallPhoto = array_merge([
            'group_id' => $groupId,
        ], $res->json());

        echo "Running photos.saveWallPhoto...\n";

        $toReturn = $vk->api(
            'photos.saveWallPhoto'
            , $paramsSaveWallPhoto
            , 'array'
            , 'post'
        );


        if(!isset($toReturn['response'][0]['id'])) {
            throw new \App\Exceptions\ArrException(
                "Vk returned invalid Request"
                , 1
                , null
                , [
                    'params' => $paramsSaveWallPhoto,
                    'response' => $toReturn
                ]);
        }
        return $toReturn['response'];
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

    public static function makePost(VK $vk, $groupId, $msg = '', $attachments = []) {
        $postRequestToVk = [
            'owner_id' => '-'.$groupId,
            'attachments' => []
        ];

        // only photos here
        // only 5 attaches max
        $attachments = array_splice($attachments, 0, 5);

        $res = \VkApiHelper::uploadPhoto($vk, $groupId, $attachments);

        $postRequestToVk['attachments'] = array_merge(
            $postRequestToVk['attachments'],
            array_map(function($item) {
                return $item['id'];
            }, $res)
        );

        if(strlen($msg) > 0) {
            $postRequestToVk['message'] = $msg;
        }
        $postRequestToVk['attachments'] = implode(',',$postRequestToVk['attachments']);

        $filesInPost = $vk->api('wall.post', $postRequestToVk, 'array', 'post');

        if(!isset($filesInPost['response']) || !isset($filesInPost['response']['post_id'])) {
            throw new RuntimeException(
                "Can not run wall.post"
            );
        }

        return $filesInPost;
    }
}