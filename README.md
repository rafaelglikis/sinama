# Sinama
Sinama is a simple web scraping library.

## Requirements
* PHP 7.0

## Installation

    composer require rafaelglikis/sinama

## Usage
Create a Sinama Client (which extends Goutte\Client):

    use  Sinama\Client;
    $client = new Client();
    
Make requests with the request() method:

    // Go to the symfony.com website
    $crawler = $client->request('GET', 'https://motherfuckingwebsite.com/');
    
The method returns a Crawler object (which extends [Symfony/Component/DomCrawler/Crawler](https://api.symfony.com/4.1/Symfony/Component/DomCrawler/Crawler.html)).

To use your own Guzzle settings, you may create and pass a new Guzzle 6 instance to Sinama Client. For example, to add a 60 second request timeout:

    use  Sinama\Client;
    use GuzzleHttp\Client as GuzzleClient;

    $client = new Client(new GuzzleClient([
        'timeout' => 60
    ]));
    $crawler = $client->request('GET', 'https://github.com/trending');

For more options visit [Guzzle Documentation](http://docs.guzzlephp.org/en/stable/request-options.html).

Click on links:
    
    $link = $crawler->selectLink('PHP')->link();
    $crawler = $client->click($link);
    echo $crawler->getUri()."\n";
    
Extract data the symfony way:

    $crawler->filter('h3 > a')->each(function ($node) {
        print trim($node->text())."\n";
    });
    
Or use Sinama special methods:
    
    $crawler = $client->request('GET', 'https://github.com/trending');
    echo '<html>';
    echo '<head>';
    echo '<title>'.$crawler->findTitle().'</title>';
    echo '<head>';
    echo '<body>';
    echo '<h1>'.$crawler->findTitle().'</h1>';
    echo '<p>Main Image: '.$crawler->findMainImage().'</p>';
    echo $crawler->findMainContent();
    echo '<pre>';
    echo 'Links: ';
    print_r($crawler->findLinks());
    echo 'Emails: ';
    print_r($crawler->findEmails());
    echo 'Images: ';
    print_r($crawler->findImages());
    echo '</pre>';
    echo '</body>';
    echo '</html>';
    
Submit forms:

    $crawler = $client->request('GET', 'https://www.google.com/');
    $form = $crawler->selectButton('Google Search')->form();
    $crawler = $client->submit($form, ['q' => 'rafaelglikis/sinama']);
    $crawler->filter('h3 > a')->each(function ($node) {
        print trim($node->text())."\n";
    });
    
## TODO
* Crawler::findTags()
    