<?php
namespace Sinama;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Goutte\Client as BaseClient;

class Client extends BaseClient
{
    /**
     * @var \Sinama\Crawler
     */
    private $sinamaCrawler;

    public function __construct(GuzzleClientInterface $client = null)
    {
        parent::__construct();

        if(is_null($client)) {
            $this->client = new GuzzleClient(array('allow_redirects' => false, 'cookies' => true));
        }
        else {
            $this->setClient($client);
        }
    }

    /**
     * Calls a URI.
     *
     * @param string $method        The request method
     * @param string $uri           The URI to fetch
     * @param array  $parameters    The Request parameters
     * @param array  $files         The files
     * @param array  $server        The server parameters (HTTP headers are referenced with a HTTP_ prefix as PHP does)
     * @param string $content       The raw body data
     * @param bool   $changeHistory Whether to update the history or not (only used internally for back(), forward(), and reload())
     *
     * @return Crawler
     */
    public function request(string $method, string $uri, array $parameters = [], array $files = [], array $server = [], string $content = null, bool $changeHistory = true)
    {
        $crawler = parent::request($method, $uri, $parameters, $files, $server, $content, $changeHistory);

        $this->sinamaCrawler = new Crawler($crawler->getNode(0), $crawler->getUri(), $crawler->getBaseHref());

        return $this->sinamaCrawler;
    }

    public function getClient()
    {
        return $this->client;
    }
}

