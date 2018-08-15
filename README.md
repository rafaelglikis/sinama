# Sinama
Sinama is a simple web scraping library.

## Requirements
* PHP 5.5+

## Installation

    composer require rafaelglikis/sinama

## Usage
Create a Sinama Client (which extends Goutte\Client):

    use  Sinama\Client;
    $client = new Client();
    
Make requests with the request() method:

    // Go to the symfony.com website
    $crawler = $client->request('GET', 'https://www.symfony.com/blog/');
    
The method returns a Crawler object (Symfony\Component\DomCrawler\Crawler).

To use your own Guzzle settings, you may create and pass a new Guzzle 6 instance to Goutte. For example, to add a 60 second request timeout:

    use  Sinama\Client;
    
    $client = new Client(new GuzzleClient([
        'base_uri' => 'https://www.symfony.com',
        'timeout' => 60
    ]));
    $crawler = $client->request('GET', '/blog/');

For more options visit [Guzzle Documentation](http://docs.guzzlephp.org/en/stable/request-options.html).