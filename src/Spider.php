<?php

namespace Sinama;


abstract class Spider
{
    /**
     * @var array
     */
    protected $followUrls = [];

    protected $lastIndex = 0;

    /**
     * @var \Sinama\Client
     */
    protected $client;

    /**
     * Spider constructor.
     *
     * @param Client|null $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client ?? new Client();

        if (is_null($client)) {
            $this->client = new Client();
        }
        else {
            $this->client = $client;
        }
        $this->followUrls = $this->getStartUrls();
    }

    /**
     * Starts the spider.
     */
    public function run()
    {
        for ($i = $this->lastIndex ; $i < count($this->followUrls); ++$i) {
            $this->lastIndex = $i;
            $crawler = $this->client->request('GET', $this->followUrls[$i]);
            $this->parse($crawler);
        }
    }

    /**
     * Implements how to parse each web page.
     *
     * @param Crawler $crawler
     * @return mixed
     */
    abstract public function parse(Crawler $crawler);

    /**
     * Implements how to scrape each web page.
     *
     * @param $url
     * @return mixed
     */
    abstract public function scrape($url);

    /**
     * Puts url in followUrls to be parsed
     *
     * @param $url
     */
    public function follow($url)
    {
        if (!in_array($url, $this->followUrls)) {
            $this->followUrls[] = $url;
        }
    }

    /**
     * Returns a list with the start urls of a spider.
     *
     * @return array
     */
    abstract public function getStartUrls(): array;

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}