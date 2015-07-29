<?php

namespace App\Vk;

class TextMethods
{

    /**
     * @link http://stackoverflow.com/questions/6004343/converting-br-into-a-new-line-for-use-in-a-text-area
     *
     * @param $txt
     *
     * @return mixed
     */
    public static function filterText($txt) {
        $breaks = array("<br />","<br>","<br/>");
        $txt = str_ireplace($breaks, "\r\n", $txt);
        return $txt;
    }


    /**
     * @link http://stackoverflow.com/questions/6127545/finding-urls-from-text-string-via-php-and-regex
     * @link http://ruskweb.ru/sots-seti/kak-sdelat-ssyilku-vkontakte-slovom-kartinkoy-na-cheloveka.html
     *
     * @param $txt
     *
     * @return bool
     */
    public static function isTextContainLinks($txt) {
        $pattern = '#(www\.|https?://)?[a-z0-9]+\.[a-z0-9]{2,4}\S*#i';

        $txt = self::filterText($txt);
        preg_match_all($pattern, $txt, $matches, PREG_PATTERN_ORDER);

        if(isset($matches[0]) && is_array($matches[0]) && count($matches[0]) > 0) {
            // then its spam post // go next
            return true;
        }

        preg_match_all('/\[\w+\|.*?\]/i', $txt, $matches1, PREG_PATTERN_ORDER);

        if(isset($matches1[0]) && is_array($matches1[0]) && count($matches1[0]) > 0) {
            // then its spam post // go next
            return true;
        }

        return false;
    }
}