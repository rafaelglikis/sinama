<?php
/**
 * Created by PhpStorm.
 * User: rafaelglikis
 * Date: 15/08/18
 * Time: 11:58 PM
 */

namespace Sinama;

use Symfony\Component\DomCrawler\Crawler as BaseCrawler;

class Crawler extends BaseCrawler
{
    public function __construct($node = null, string $uri = null, string $baseHref = null)
    {
        parent::__construct($node, $uri, $baseHref);
    }
}