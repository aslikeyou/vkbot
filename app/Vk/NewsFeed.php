<?php


namespace App\Vk;

use VK\VK;
use App\Exceptions\AlreadyExistException;
use App\Exceptions\InvalidResponseException;
use App\Exceptions\SpamPostException;

class NewsFeed
{
    public static function getLastPostFromGroup(VK $vk, $uid) {
    $newsfeedGetresponse = $vk->api('newsfeed.get', $reqParams = [
        'source_ids' => $uid,
        'filter'     => 'post',
        //'count'    => 5,
    ]);

    if ( ! isset($newsfeedGetresponse['response']['items'])) {
        throw new InvalidResponseException(
            'newsfeed.get'
            , $reqParams
            , $newsfeedGetresponse
        );
    }

    $fileteredItems
        = array_filter($newsfeedGetresponse['response']['items'],
        function ($row) {
            return isset($row['type']) && isset($row['post_type'])
            && $row['type'] === "post"
            && $row['post_type'] === "post";
        });

    $firstFilteredItem = array_values($fileteredItems)[0];
    return $firstFilteredItem;
}

}