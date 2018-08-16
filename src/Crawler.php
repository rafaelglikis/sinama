<?php

namespace Sinama;

use Symfony\Component\DomCrawler\Crawler as BaseCrawler;

class Crawler extends BaseCrawler
{
    public function __construct($node = null, string $uri = null, string $baseHref = null)
    {
        parent::__construct($node, $uri, $baseHref);
    }

    public function findTitle()
    {
        return trim($this->filter('title')->text());
    }

    public function filterLinks()
    {
        $links = [];
        $atags = $this->getNode(0)->getElementsByTagName('a');

        for ($i=0; $i<$atags->count(); ++$i) {
            $links[] = trim($atags->item($i)->getAttribute('href'));
        }

        return $links;
    }

    public function findMainImage()
    {
        // Try to get og:image from meta
        $metas = $this->getNode(0)->getElementsByTagName('meta');
        $image = NULL;
        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);
            if($meta->getAttribute('property') == 'og:image') {
                $image = $meta->getAttribute('content');
                $image = Utils::makeUrlIfNot($image, $this->getUri());
                if(filter_var($image, FILTER_VALIDATE_URL)) {
                    return $image;
                }
            }
        }

        // If og:image not exists get a random image from DOM
        $images = $this->getNode(0)->getElementsByTagName('img');
        foreach ($images as $image) {
            $image = $image->getAttribute('src');
            $image = Utils::makeUrlIfNot($image, $this->getUri());
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                return $image;
            }
        }

        return null;
    }

    public function extractLinks()
    {
        $links = $this->filterLinks();

        foreach ($links as &$link) {
            if(!Utils::isValidUrl($link)) {
                $link = $this->getBaseHref().$link;
            }
        }

        return $links;
    }

    public function extractEmails()
    {
        $regexp = '/([a-z0-9_\.\-])+\@(([a-z0-9\-])+\.)+([a-z0-9]{2,4})+/i';
        preg_match_all($regexp, $this->html(), $matches);

        return $matches[0] ?? [];
    }
}