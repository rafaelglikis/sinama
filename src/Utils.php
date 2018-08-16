<?php

namespace Sinama;


class Utils
{
    public static function isLink(string $str): string
    {
        return strpos(strtolower($str),"http://") === 0 ||
            strpos(strtolower($str),"https://") === 0;
    }

    public static function makeUrlIfNot(string $url, string $baseUri): string
    {
        if (!Utils::isLink($url) && $baseUri) {
            $parse = parse_url($baseUri);
            $domain = $parse['host'];
            $url = $parse['scheme'].'://'.$domain.'/'.$url;
        }
        return $url;
    }

    /**
     * Remove unnecessary html tags
     * (scripts, styles, ads, etc)
     * @param $html
     * @return string
     */
    public static function fixHtml(string $html): string
    {
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $html);
        $html = strip_tags($html, '<p><strong><a><iframe><img><ul><ol><li><br><h1><h2><h3><h4>');

        return $html;
    }

    /**
     * Returns the string value from data between start - end
     * @param string $str
     * @param string|null $start
     * @param string|null $end
     * @return string
     */
    public static function cut(string $str, string $start = null, string $end = null)
    {
        if ($end == null)
        {
            $intStart = @strpos($str,$start) + strlen($start);
            $cut = @substr($str,$intStart);
            return $cut;
        }
        $intStart = @strpos($str,$start) + strlen($start);
        $cut = @substr($str,$intStart);
        $intEnd = @strpos($cut,$end);
        $cut = @substr($cut,0,$intEnd);
        return $cut;
    }

    public static function diff(string $str1, string $str2)
    {
        // Converting strings to arrays
        $arr1 = explode(" ", $str1);
        $arr2 = explode(" ", $str2);

        return join(" \n", array_diff($arr2, $arr1));
    }
}