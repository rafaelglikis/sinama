<?php

namespace Sinama;


interface SpiderInterface
{

    public function parse(string $url);

    public function scrape(string $url);

    public function follow(string $url);

    public function run();
}