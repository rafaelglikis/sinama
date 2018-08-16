<?php

namespace Sinama\Tests;


use PHPUnit\Framework\TestCase;
use Sinama\Crawler;

class CrawlerTest extends TestCase
{
    /**
     * @var \Sinama\Crawler
     */
    private static $crawler;

    public static function setUpBeforeClass()
    {
        CrawlerTest::$crawler = new Crawler(file_get_contents("fixtures/test.txt"), "http://www.mfw.com");
    }

    public function testFindTitle()
    {
        $this->assertEquals('Motherfucking Website', CrawlerTest::$crawler->findTitle());
    }

    public function testFindMainImage()
    {
        $this->assertEquals('https://www.w3schools.com/html5.gif', CrawlerTest::$crawler->findMainImage());
    }

    public function testFindMainContent()
    {
        $space='                    ';
        $p = $space."<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vitae pretium augue. Quisque viverra dui non enim commodo auctor. Sed.</p>\n";
        $expexted = $p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p;
        $this->assertEquals(trim($expexted), trim(CrawlerTest::$crawler->findMainContent()));
    }

    public function testFindImages()
    {
        $expectedImages = [
            'https://www.w3schools.com/html5.gif',
            'https://www.w3schools.com/html5.gif',
            'https://www.w3schools.com/pic_trulli.jpg',
            'https://www.w3schools.com/img_chania.jpg',
            'https://www.w3schools.com/img_girl.jpg'
        ];

        $this->assertEquals($expectedImages, CrawlerTest::$crawler->findImages());
    }

    public function testFindEmails()
    {
        $expectedEmails = [
            'test@test.com',
            'test1@test.com',
            'test2@test.com',
            'test3@test.com',
            'test4@test.com',
            'test5@test.com',
            'test6@test.com',
            'test7@test.com',
            'test8@test.com',
            'test9@test.com',
            'test10@test.com',
            'test11@test.com'
        ];

        $this->assertEquals($expectedEmails, CrawlerTest::$crawler->findEmails());
    }
}