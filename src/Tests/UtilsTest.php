<?php

namespace Sinama\Tests;

use DOMDocument;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\CodeCoverage\Util;
use Sinama\Utils;

class UtilsTest extends TestCase
{
    private static  $html;

    public static function setUpBeforeClass()
    {
        UtilsTest::$html = file_get_contents("fixtures/test.html");
    }

    public function testIsValidUrl()
    {
        $this->assertEquals(true, Utils::isValidUrl('http://www.github.com'));
        $this->assertEquals(true, Utils::isValidUrl('https://www.github.com'));
        $this->assertEquals(false, Utils::isValidUrl('www.github.com'));
        $this->assertEquals(false, Utils::isValidUrl('thub.com'));
        $this->assertEquals(false, Utils::isValidUrl('savarakatranemia'));
    }

    public function testIsValidEmail()
    {
        $this->assertEquals(false, Utils::isValidEmail('savarakatranemia'));
        $this->assertEquals(true, Utils::isValidEmail('rafaelglikis@gmail.com'));
        $this->assertEquals(false, Utils::isValidEmail('rafaelglikis@gmail'));
        $this->assertEquals(false, Utils::isValidEmail('@gmail'));
    }

    public function testIsValidIpAddress()
    {
        $this->assertEquals(false, Utils::isValidIpAddress('savarakatranemia'));
        $this->assertEquals(false, Utils::isValidIpAddress('231.321.32'));
        $this->assertEquals(false, Utils::isValidIpAddress('256.1.1.1'));
        $this->assertEquals(true, Utils::isValidIpAddress('255.255.255.255'));
        $this->assertEquals(true, Utils::isValidIpAddress('127.0.0.1'));
        $this->assertEquals(true, Utils::isValidIpAddress('174.192.47.1'));
    }
    public function testIsValidJson()
    {
        $this->assertEquals(false, Utils::isValidJson('savarakatranemia'));
        $this->assertEquals(false, Utils::isValidJson('{savarakatranemia}'));
        $this->assertEquals(false, Utils::isValidJson('{savarakatranemia: savarakatranemia}'));
        $this->assertEquals(true, Utils::isValidJson('{"savarakatranemia": "savarakatranemia"}'));
    }

    public function testExtractEmails()
    {
        $str = UtilsTest::$html;
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

        $this->assertEquals($expectedEmails, Utils::extractEmails($str));
    }

    public function testExtractImages()
    {
        $str = UtilsTest::$html;
        $expectedImages = [
            'https://www.w3schools.com/html5.gif',
            'https://www.w3schools.com/html5.gif',
            'https://www.w3schools.com/pic_trulli.jpg',
            'https://www.w3schools.com/img_chania.jpg',
            'https://www.w3schools.com/img_girl.jpg'
        ];

        $this->assertEquals($expectedImages, Utils::extractImages($str));
    }

    public function testMakeUrlIfNot()
    {
        $baseUrl = 'http://www.github.com';
        $this->assertEquals($baseUrl, Utils::makeUrlIfNot('http://www.github.com', $baseUrl));
        $this->assertEquals($baseUrl.'/blog/', Utils::makeUrlIfNot('blog/', $baseUrl));
        $this->assertEquals($baseUrl.'/blog/savarakatranemia-post', Utils::makeUrlIfNot('blog/savarakatranemia-post', $baseUrl));
    }

    public function testExtractTopNode()
    {
        $dom = new DOMDocument;
        $dom->appendChild($dom->importNode(Utils::extractTopNode(UtilsTest::$html), true));
        $str = $dom->saveHTML();
        $space='                    ';
        $p = $space."<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vitae pretium augue. Quisque viverra dui non enim commodo auctor. Sed.</p>\n";
        $expexted = "<div class=\"entry\">\n"
            .$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p.$p
            ."                </div>\n";
        $this->assertEquals($expexted, $str);
    }

    public function testCut()
    {
        $str = '<p>inner</p>';
        $this->assertEquals('inner', Utils::cut($str, '<p>', '</p>'));
        $this->assertEquals('inner</p>', Utils::cut($str, '<p>'));
        $this->assertEquals('<p>inner', Utils::cut($str, null, '</p>'));

        $expectedStr = 'You probably build websites and think your shit is special. You think your 13 megabyte parallax-ative home page is going to get you some fucking Awwward banner you can glue to the top corner of your site. You think your 40-pound jQuery file and 83 polyfills give IE7 a boner because it finally has box-shadow. Wrong, motherfucker. Let me describe your perfect-ass website:';
        $this->assertEquals($expectedStr, Utils::cut(UtilsTest::$html, '<p>', '</p>'));
    }

    public function testCutAll()
    {
        $str = Utils::cut(UtilsTest::$html, ' <article', ' </article>');
        $results = Utils::cutAll($str, '<p>', '</p>');
        $innerP = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vitae pretium augue. Quisque viverra dui non enim commodo auctor. Sed.";

        foreach ($results as $res) {
            $this->assertEquals($innerP, $res);
        }
    }

    public function testDiff()
    {
        $str1 = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus vitae pretium augue. Quisque viverra dui non enim commodo auctor. Sed.";
        $str2 = "Lorens ipsum dolor sil amet, consectetur adipiscing elit. Phasellus vitae pretium augue. Quisque viverra dui non enim commodo auctor. Sed. savarakatranemia";
        $this->assertEquals('', Utils::diff($str1, $str1));
        $this->assertEquals("+ Lorem\n- Lorens\n+ sit\n- sil\n- savarakatranemia\n", Utils::diff($str1, $str2));
        $this->assertEquals("+ Lorens\n- Lorem\n+ sil\n- sit\n+ savarakatranemia\n", Utils::diff($str2, $str1));

        $str1 = "Lorem ipsum dolor sit amet.\n Lorem ipsum dolor sit amet.\n Lorem ipsum dolor sit amet\n";
        $str2 = "Lorem ipsum dolor sit amel.\n Lorem ipsum dolor sit amet.\n Lorem ipsum dolor sit amet\n";

        $this->assertEquals('', Utils::diff($str1, $str1, "\n"));
        echo "\n".Utils::diff($str1, $str2, "\n");
        $this->assertEquals("+ Lorem ipsum dolor sit amet.\n- Lorem ipsum dolor sit amel.\n", Utils::diff($str1, $str2, "\n"));
    }

}