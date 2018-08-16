<?php

namespace Sinama;


class Utils
{
    public static function isLink($str)
    {
        return strpos(strtolower($str),"http://") === 0 ||
            strpos(strtolower($str),"https://") === 0;
    }

    public static function makeUrlIfNot($url, $baseUri)
    {
        if (!Utils::isLink($url) && $baseUri) {
            $parse = parse_url($baseUri);
            $domain = $parse['host'];
            $url = $parse['scheme'].'://'.$domain.'/'.$url;
        }
        return $url;
    }
}