<?php

namespace Sinama;

use Symfony\Component\DomCrawler\Crawler as BaseCrawler;
use DOMDocument;

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

    public function findLinks()
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
                $image = Utils::makeUrlIfNot($image, $this->getBaseUrl());
                if(filter_var($image, FILTER_VALIDATE_URL)) {
                    return $image;
                }
            }
        }

        // If og:image not exists get a random image from DOM
        $images = $this->getNode(0)->getElementsByTagName('img');
        foreach ($images as $image) {
            $image = $image->getAttribute('src');
            $image = Utils::makeUrlIfNot($image, $this->getBaseUrl());
            if (filter_var($image, FILTER_VALIDATE_URL)) {
                return $image;
            }
        }

        return null;
    }

    /**
     * Finds the main content of the given html code
     * (Uses Heuristic not 100% accurate)
     * @param $html
     * @return string
     */
    public function findMainContent(): string
    {
        $html = $this->html();
        $mainContentNode = Utils::findTopNode($html);
        // DOMDocument
        $Target = new DOMDocument;
        $Target->appendChild($Target->importNode($mainContentNode, true));
        // $mainContent = $Target->saveHTML();
        $mainContent = mb_convert_encoding($Target->saveHTML(),  "utf-8", "HTML-ENTITIES");
        $mainContent = Utils::fixHtml($mainContent);

        return $mainContent;
    }

    public function findImages()
    {
        return Utils::extractImages($this->html());
    }

    public function findEmails()
    {
        return Utils::extractEmails($this->html());
    }

    public function getBaseUrl()
    {
        $url_info = parse_url($this->getUri());
        return $url_info['scheme'] . '://' . $url_info['host'];
    }
}