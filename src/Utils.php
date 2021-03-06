<?php

namespace Sinama;


use DOMDocument;
use DOMElement;

class Utils
{
    // Regular Expressions
    const EMAIL_REGEX = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';

    const IRRELEVANT_ID_REGEX = "/(comment|meta|footer|footnote)/i";
    const IRRELEVANT_CLASS_REGEX = "/(comment|meta|footer|footnote)/i";

    const SPECIAL_ID_REGEX = "/((^|\\s)(post|hentry|entry[-]?(content|text|body)?|article[-]?(content|text|body)?)(\\s|$))/i";
    const SPECIAL_CLASS_REGEX = "/^(post|hentry|entry[-]?(content|text|body)?|article[-]?(content|text|body)?)$/i";

    // Heuristics
    const SPECIAL_CLASS_NAME_SCORE = 25;
    const SPECIAL_ID_NAME_SCORE = 25;

    const IRRELEVANT_CLASS_NAME_SCORE = -50;
    const IRRELEVANT_ID_NAME_SCORE = -50;

    public static function isValidUrl(string $str): string
    {
        return (bool)filter_var($str, FILTER_VALIDATE_URL);
    }

    public static function isValidEmail(string $str): string
    {
        return (bool)filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    public static function isValidIpAddress(string $str): string
    {
        return (bool)filter_var($str, FILTER_VALIDATE_IP);
    }

    /**
     * Returns true if string is valid json.
     *
     * @param $str
     *
     * @return bool
     */
    static function isValidJson(string $str)
    {
        // decode the JSON data
        $result = json_decode($str);

        return (json_last_error() === JSON_ERROR_NONE);
    }

    public static function extractEmails(string $str)
    {
        preg_match_all(Utils::EMAIL_REGEX, $str, $matches);

        return $matches[0] ?? [];
    }

    public static function extractImages(string $html)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);

        $images = [];

        $imgTags = $dom->getElementsByTagName('img');
        foreach ($imgTags as $img) {
            $img = $img->getAttribute('src');
            $images[] =  $img;
        }

        return $images;
    }

    /**
     * Adds baseUri to url is not url.
     *
     * @param string $url
     * @param string $baseUri
     *
     * @return string
     */
    public static function makeUrlIfNot(string $url, string $baseUri): string
    {
        if (!Utils::isValidUrl($url) && $baseUri) {
            $parse = parse_url($baseUri);
            $domain = $parse['host'];
            $url = $parse['scheme'].'://'.$domain.'/'.$url;
        }
        return $url;
    }

    /**
     * Finds the main node of given html.
     * (Uses Heuristic not 100% accurate)
     * @param $html
     * @return DOMElement
     */
    public static function extractTopNode(string $html) : DOMElement
    {
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        // Study all the paragraphs and calculate the score of parentNode
        // Score is determined by heuristics.
        $nodes = array();
        $paragraphs = $dom->getElementsByTagName("p");
        foreach ($paragraphs as $paragraph) {
            $contentScore = Utils::calculateParagraphScore($paragraph);
            $paragraph->parentNode->setAttribute("contentScore", $contentScore);
            $nodes[] = $paragraph->parentNode;
        }
        /**
         * @var $topNode DOMElement
         */
        $topNode = $nodes[0];
        // Find the node with the higher score
        foreach ($nodes as $node) {
            $contentScore = intval($node->getAttribute("contentScore"));
            $higherContentScore = intval($topNode->getAttribute("contentScore"));
            if ($contentScore && $contentScore > $higherContentScore) {
                $topNode = $node;
            }
        }
        $topNode->removeAttribute("contentScore");

        return $topNode;
    }

    /**
     * Calculates the score of the given paragraph.
     * Score is determined by heuristics.
     * @param $paragraph
     * @return int
     */
    private static function calculateParagraphScore(DOMElement $paragraph): int
    {
        $parentNode = $paragraph->parentNode;
        $contentScore = intval($parentNode->getAttribute("contentScore"));
        // Look for a special classname
        $className = $parentNode->getAttribute("class");
        if (preg_match(Utils::IRRELEVANT_ID_REGEX, $className)) {
            $contentScore += Utils::IRRELEVANT_CLASS_NAME_SCORE;
        } else if(preg_match(Utils::SPECIAL_ID_REGEX, $className)) {
            $contentScore += Utils::SPECIAL_CLASS_NAME_SCORE;
        }
        // Look for a special ID
        $id = $parentNode->getAttribute("id");
        if (preg_match(Utils::IRRELEVANT_CLASS_REGEX, $id)) {
            $contentScore += Utils::IRRELEVANT_ID_NAME_SCORE;
        } else if (preg_match(Utils::SPECIAL_CLASS_REGEX, $id)) {
            $contentScore += Utils::SPECIAL_ID_NAME_SCORE;
        }
        // Add paragraph length to score
        if (strlen($paragraph->nodeValue) > 10) {
            $contentScore += strlen($paragraph->nodeValue);
        }
        return $contentScore;
    }

    /**
     * Remove unnecessary html tags (scripts, styles, ads, etc)
     * @param $html
     *
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
     *
     * @param string $str
     * @param string|null $start
     * @param string|null $end
     *
     * @return string
     */
    public static function cut(string $str, string $start = null, string $end = null): string 
    {
        if ($end == null) {
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

    public static function cutAll(string $str, string $start, string $end): array
    {
        $pieces = [];
        $remainingStr = $str;

        $remainingStr = Utils::cut($remainingStr, $start);
        $piece = Utils::cut($remainingStr, null, $end);

        while ($piece!='') {
            $pieces[] = $piece;
            $remainingStr = Utils::cut($remainingStr, $start);
            $piece = Utils::cut($remainingStr, null, $end);
            $remainingStr = Utils::cut($remainingStr, $end);
        }

        return $pieces;
    }

    public static function diff(string $str1, string $str2, $delimiter = ' ')
    {
        // Converting strings to arrays
        $arr1 = explode($delimiter, $str1);
        $arr2 = explode($delimiter, $str2);
        $arrDiff = array_map(null, array_diff($arr1, $arr2), array_diff($arr2, $arr1));

        $strDiff = '';
        foreach ($arrDiff as $res) {
            if (isset($res[0])) {
                $strDiff.="+ $res[0]\n";
            }
            if (isset($res[1])) {
                $strDiff.="- $res[1]\n";
            }
        }
        return $strDiff;
    }
}