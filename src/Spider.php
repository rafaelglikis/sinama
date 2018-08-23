<?php

namespace Sinama;

abstract class Spider implements SpiderInterface
{
    /**
     * @var array
     */
    private $followUrls = [];

    /**
     * @var int
     */
    private $maxDepth = -1;

    /**
     * @var int
     */
    private $lastIndex = 0;

    /**
     * @var float
     */
    private $sleepTime = 0;

    /**
     * @var boolean
     */
    private $verbose = false;

    /**
     * @var \Sinama\Client
     */
    protected $client;

    /**
     * Spider constructor.
     *
     * @param array $params
     * @param Client|null $client
     */
    public function __construct($params = [], Client $client = null)
    {
        $this->client = $client ?? new Client();

        // Setting parameters
        $this->followUrls = $params['start_urls'] ?? [];
        $this->maxDepth =  (int)$params['max_depth'] ?? -1;
        $this->verbose = (bool)$params['verbose'] ?? false;
    }

    /**
     * Starts the spider.
     */
    public function run()
    {
        $this->log('i', 'Spider started');

        for ($i = $this->lastIndex ; $i < count($this->followUrls); ++$i) {
            $this->log('i', 'Parsing ' . $this->followUrls[$i]);

            $this->lastIndex = $i;
            $this->parse($this->followUrls[$i]);
            if ( $i == $this->maxDepth) {
                $this->log('i', 'Max depth reached');
                break;
            }
        }

        $this->log('i', 'End of site reached');
    }

    /**
     * Implements how to parse each web page.
     *
     * @param string $url
     * @return mixed
     */
    abstract public function parse(string $url);

    /**
     * Implements how to scrape each web page.
     *
     * @param $url
     * @return mixed
     */
    abstract public function scrape(string $url);

    /**
     * Puts url in followUrls to be parsed
     *
     * @param $url
     */
    public function follow(string $url)
    {
        if (!in_array($url, $this->followUrls)) {
            $this->followUrls[] = $url;
        }
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }

    private function log($type, $message)
    {
        if ($this->verbose) {
            $time = date('Y:m:d:h:i:s');
            echo "[$type] [$time] $message\n";
        }
    }
}