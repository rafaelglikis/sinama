<?php
namespace Sinama;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use Goutte\Client as BaseClient;

class Client extends BaseClient
{
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

    public function getClient()
    {
        return $this->client;
    }
}

