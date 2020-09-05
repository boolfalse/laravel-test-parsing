<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Session;

use Goutte\Client;


/*
http://obmen.effio.xyz/property/159
https://www.cian.ru/


http://stackoverflow.com/questions/15761115/find-div-with-class-using-php-simple-html-dom-parser
http://simplehtmldom.sourceforge.net/manual.htm
http://stackoverflow.com/questions/6366351/getting-dom-elements-by-classname
http://stackoverflow.com/questions/38301501/how-to-scrape-html-content-of-div-using-all-matched-class
https://github.com/wasinger/htmlpagedom
https://github.com/FriendsOfPHP/Goutte
http://stackoverflow.com/questions/15200826/scraping-complete-web-site-for-data-within-specific-div-tag-where-url-includes-s
https://code.tutsplus.com/tutorials/html-parsing-and-screen-scraping-with-the-simple-html-dom-library--net-11856
http://stackoverflow.com/questions/18316093/get-content-of-special-html-tag-by-class-name
http://wern-ancheta.com/blog/2013/08/07/getting-started-with-web-scraping-in-php/
http://simplehtmldom.sourceforge.net/
https://laracasts.com/discuss/channels/general-discussion/search-html-between-certain-html-tagsmarkup-w-php
https://facepunch.com/showthread.php?t=1503733&p=49810310&viewfull=1
https://www.2basetechnologies.com/screen-scraping-with-xpath-in-php
https://codecanyon.net/tags/parser


 */


class CrawlingController extends Controller
{
    // Goutte
    public function crawl0(){
        $crawler = \Goutte::request('GET', 'http://www.globusmedical.com/product-room/');
        $crawler->filter('.portfolio-media-inner-wrapper a')->each(function ($node) {
            //dump($node->text());
            dd($node);
        });
    }
    // + DOMDocument
    public function crawl1(){
        $url = 'http://www.globusmedical.com/product-room/';
        $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
        $context = stream_context_create($opts);
        $html = file_get_contents($url,false,$context);
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $links = $dom->getElementsByTagName('a');
        $link_prefix = 'portfolio';
        foreach ($links as $link)
        {
            $current = $link->getAttribute('href');
            if (strpos($current, $link_prefix) !== false) {
                echo $current."<br />";
            }
        }
    }
    // DOMDocument
    public function crawl2(){
        $url = 'http://www.globusmedical.com/product-room/';
        $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
        $context = stream_context_create($opts);
        $html = file_get_contents($url,false,$context);
        $dom=new DOMDocument();
        $dom->loadHTML( $html);       /*$str contains html output */
        $xpath=new DOMXPath($dom);
        $imgfind=$dom->getElementsByTagName('img');  /*finding elements by tag name img*/
        foreach($imgfind as $im) {
            echo "<img src=".$im->getAttribute('src')."/>"; //use this instead of echo $im->src;
        }
        //$printimage=$xpath->query('//div[@class="abc"]');
        $printimage=$xpath->query('string(//img/@src)');
        foreach($printimage as $image) {
            echo $image->src;   //still i could not accomplish my task
        }
        dd(36);
    }
    // DOMDocument
    public function crawl3(){
        $url = 'http://www.globusmedical.com/product-room/';
        $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
        $context = stream_context_create($opts);
        $html = file_get_contents($url,false,$context);

        $doc = new DOMDocument();
        $doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);
        $src = $xpath->evaluate("string(//img/@src)");
        dd($src);
    }
    // + not need    DOMDocument
    public function crawl4(){
        $url = 'http://www.globusmedical.com/product-room/';
        $opts = array('http'=>array('header' => "User-Agent:MyAgent/1.0\r\n"));
        $context = stream_context_create($opts);
        $html = file_get_contents($url,false,$context);
        $dom = new \DOMDocument();
        // http://stackoverflow.com/questions/12328322/php-domdocumentloadhtml-domdocument-loadhtml-htmlparseentityref-no-name
        @$dom->loadHTML( $html); // libxml_use_internal_errors(true);
        foreach ($dom->getElementsByTagName('a') as $node) {
            echo $dom->saveHtml($node), PHP_EOL;
        }
    }
    // + DOMDocument
    public function crawl5(){
        $url = 'http://www.globusmedical.com/product-room/';
        $dom_xml = new \DOMDocument();
        @$dom_xml->loadHTMLFile($url);
        $link_prefix = 'portfolio';
        foreach($dom_xml->getElementsByTagName('a') as $link) {
            $current = $link->getAttribute('href');
            if (strpos($current, $link_prefix) !== false) {
                echo $current."<br />";
            }
        }
    }
    // DOMDocument with finder DomXPath
    public function crawl6(){
        $url = 'http://www.globusmedical.com/product-room/';
        $dom = new DomDocument();
        @$dom->load($url);
        $finder = new DomXPath($dom);
        $classname="portfolio-item";
        $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), 'portfolio-item')]");
        dd($nodes);
//        $url = 'http://www.globusmedical.com/product-room/';
//        $dom = new DomDocument();
//        @$dom->load($url);
//        $finder = new DomXPath($dom);
//        $classname="portfolio-item";
//        $nodes = $finder->query("//*[contains(@class, '$classname')]");
//        $finder = new Zend_Dom_Query($html);
//        $classname = 'portfolio-item';
//        $nodes = $finder->query("*[class~=\"$classname\"]");
    }
    // DOMDocument with finder DomXPath
    public function crawl7(){
        $result = array();
        $url = 'http://www.globusmedical.com/product-room/';
        $classname = "clear";
        $domdocument = new DOMDocument();
        @$domdocument->loadHTML($url);
        $a = new DOMXPath($domdocument);
        $spans = $a->query("//*[contains(concat(' ', normalize-space(@class), ' '), '$classname')]");
        for ($i = $spans->length - 1; $i > -1; $i--) {
            $result[] = $spans->item($i)->firstChild->nodeValue;
        }
        echo "<pre>";
        print_r($result);
        echo "</pre>";
        exit();
    }
    // DOMDocument
    public function crawl8(){
        $url = 'http://www.globusmedical.com/product-room/'; // http://php.net/manual/en/class.domdocument.php
        $dom_xml = new DOMDocument(); // Create a new DOM Document to hold our webpage structure
        @$dom_xml->loadHTMLFile($url); // Load the url's contents into the DOM
        $links = array();
        foreach($dom_xml->getElementsByTagName('a') as $link) {
            $links[] = array('url' => $link->getAttribute('href'), 'text' => $link->nodeValue);
        }
        echo '<pre>';
        var_dump($links);
        echo '</pre>';
//        $classname="blockProduct";
//        $finder = new DomXPath($doc);
//        $spaner = $finder->query("//*[contains(@class, '$classname')]");
    }
    // ? DOMDocument with finder DomXPath
    public function crawl9(){
        // http://stackoverflow.com/questions/18316093/get-content-of-special-html-tag-by-class-name
        $html = file_get_contents('http://www.globusmedical.com/product-room/');
        $doc = new DOMDocument();
        @$doc->loadHTML($html);
        $finder = new DomXPath($doc);
        foreach ($finder->query("//*[contains(@class, 'portfolio-media-inner-wrapper')]") as $node){
            $node = $finder->query("//*[contains(@class, 'portfolio-media-inner-wrapper')]");
            print_r($doc->saveHTML($node->item(0)));
        }
    }
    // ? DOMDocument with finder DomXPath
    public function crawl10(){
        $url = 'http://www.globusmedical.com/product-room/';
        $dom = new DomDocument();
        @$dom->loadHTML($url);
//        $finder = new DomXPath($dom);
//        $classname="portfolio-item";
//        $nodes = $finder->query("//div[contains(@class, '$classname')]");
        $finder = new DomXPath($dom);
        $classname = 'my-class';
        $nodes = $finder->query("*[class~=\"$classname\"]");
        dd($nodes);
    }
    // + no need     Client
    public function crawl11(){
        $client = new Client();
        $url = 'http://www.globusmedical.com/product-room/';
        // Go to the symfony.com website
        $crawler = $client->request('GET', $url);
        $crawler->filter('.portfolio-item-holder a')->each(function ($node) {
            print $node->text()."<br />";
        });
    }
    // Crawler
    public function crawl12(){
        $url = 'http://www.globusmedical.com/product-room/';
        $crawler = new Crawler();
        $crawler->addHtmlContent($url);
        $crawler->filterXPath('//a[contains(@href, "portfolio")]')->evaluate('@href');
        dd($crawler);
    }

    // ------------------------ 20022017

    // + DOMDocument
    public function crawl13()
    { // http://stackoverflow.com/questions/4048070/php-function-to-grab-all-links-inside-a-div-on-remote-site-using-scrape-method
        $url = 'http://www.globusmedical.com/product-room/';
        $html = file_get_contents($url);
        $dom = new \DOMDocument(); // Create a new DOM document
        libxml_use_internal_errors(true);
        $dom->loadHTML($html); // Parse the HTML. The @ is used to suppress any parsing errors, that will be thrown if the $html string isn't valid XHTML.
        $links = $dom->getElementsByTagName('a'); // Get all links. You could also use any other tag name here, like 'img' or 'table', to extract other tags.
        $link_prefix = 'portfolio';
        foreach ($links as $link) // Iterate over the extracted links and display their URLs
        {
            $current = $link->getAttribute('href'); // Extract and show the "href" attribute.
            if (strpos($current, $link_prefix) !== false) {
                echo $current."<br />";
            }
        }
    }
    // + DOMDocument with finder DomXPath
    public function crawl14(){
        // http://stackoverflow.com/questions/38301501/how-to-scrape-html-content-of-div-using-all-matched-class
        $html = new \DOMDocument();
        @$html->loadHtmlFile('http://php.net/manual/de/domdocument.savehtml.php');
        $xpath = new \DOMXPath( $html );
        $nodelist = $xpath->query( '//footer' );
        foreach ($nodelist as $n){
            echo $html->saveHtml($n)."<br />";
        }
    }
    // + Client
    public function crawl15(){
        // http://pastebin.com/EEP03x9X
        for($i=0; $i<=2; $i++)
        {
            $p = new Client();
            $d = $p->request('GET', 'http://www.oglaszamy24.pl/ogloszenia/nieruchomosci/domy/?std=1&results='. $i);
            $d->filter('a[class="o_title"]')->each(function ($node)
            {
                $p1 = new Client();
                $d1 = $p1->request('GET', $node->attr('href'));
                $d1->filter('div[id="adv_desc"]')->each(function ($tekst) { $txt = $tekst->text().'<br>';
                });
                echo '<b>'.$hrefs[] = $node->attr('href').'</b><br>';
            });
            echo $i.'<br>';
        }
    }
    // + DOMDocument with finder DomXPath
    public function crawl16(){
        $html = file_get_contents('http://pokemondb.net/evolution'); //get the html returned from the following url
        $pokemon_doc = new \DOMDocument();
        libxml_use_internal_errors(TRUE); //disable libxml errors
        if(!empty($html)){ //if any html is actually returned
            $pokemon_doc->loadHTML($html);
            libxml_clear_errors(); //remove errors for yucky html
            $pokemon_xpath = new \DOMXPath($pokemon_doc);
            //get all the h2's with an id
            $pokemon_row = $pokemon_xpath->query('//h2[@id]');
            if($pokemon_row->length > 0){
                foreach($pokemon_row as $row){
                    echo $row->nodeValue."<br/>";
                }
            }
        }
    }
    // + DOMDocument with finder DomXPath
    public function crawl17(){
        // https://laracasts.com/discuss/channels/general-discussion/search-html-between-certain-html-tagsmarkup-w-php
        $url = 'http://www.globusmedical.com/portfolio/affirm/';
        $data = file_get_contents($url);
        $dom = new \DOMDocument();
        @$dom->loadHTML($data); // $data is your html code, grab it using file_get_contents or cURL.
        $xpath = new \DOMXPath($dom);
        $div = $xpath->query('//div[@class="pf-content"]');
        $div = $div->item(0);
        echo $dom->saveXML($div);
    }
	
	public function test1(){
		$urls = []; //custom definition

		// FUNCTIONS

		// SCRIPT BODY

		for ($i=1; $i<100; $i++){
			$urls[] = "http://httpbin.org/get?i=".$i;
		}

		$multi = curl_multi_init();
		$handles = []; //custom definition
		foreach ($urls as $url){
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_multi_add_handle($multi, $ch);
			$handles[$url] = $ch;
		}

		do{
			$mrc = curl_multi_exec($multi, $active);
		} while($mrc == CURLM_CALL_MULTI_PERFORM);

		while($active && $mrc==CURLM_OK){
			// check for results and execute until everything is done
			if(curl_multi_select($multi) == -1){
				// if it returns -1, wait a bit, but go forward anyways!
				usleep(100);
			}
			do{
				$mrc = curl_multi_exec($multi, $active);
			} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		}

		foreach ($handles as $channel){
			$html = curl_multi_getcontent($channel);
			echo '<pre>';
			var_dump($html);
			echo '</pre>';
			curl_multi_remove_handle($multi, $channel);
		}

		curl_multi_close($multi);


		// **************************************************************************
		// we will get something like this as result
		//      string(173) "{
		//        "args": {
		//          "i": "1"
		//        },
		//        "headers": {
		//          "Accept": "*/*",
		//          "Host": "httpbin.org"
		//        },
		//        "origin": "37.157.223.189",
		//        "url": "http://httpbin.org/get?i=1"
		//      }
		//      "
		//      ...



		// **************************************************************************
		// for "http://httpbin.org/get?i=1" we will get this
		//
		//      {
		//          "args": {
		//          "i": "1"
		//        },
		//        "headers": {
		//          "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
		//          "Accept-Encoding": "gzip, deflate, sdch",
		//          "Accept-Language": "ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4",
		//          "Host": "httpbin.org",
		//          "Upgrade-Insecure-Requests": "1",
		//          "User-Agent": "Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36"
		//        },
		//        "origin": "37.157.223.189",
		//        "url": "http://httpbin.org/get?i=1"
		//      }
	}
	
	public function test2(){
		$urls = []; //custom definition

		// FUNCTIONS
		function multirequest($urls){
			// initializations
			$multi = curl_multi_init();
			$handles = [];
			$htmls = [];

			foreach ($urls as $url){
				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_multi_add_handle($multi, $ch);
				$handles[$url] = $ch;
			}
			do{
				$mrc = curl_multi_exec($multi, $active);
			} while($mrc == CURLM_CALL_MULTI_PERFORM);

			while($active && $mrc==CURLM_OK){
				// check for results and execute until everything is done
				if(curl_multi_select($multi) == -1){
					// if it returns -1, wait a bit, but go forward anyways!
					usleep(100);
				}
				do{
					$mrc = curl_multi_exec($multi, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}

			foreach ($handles as $channel){
				$html = curl_multi_getcontent($channel);
				$htmls[] = $html;
				curl_multi_remove_handle($multi, $channel);
			}
			curl_multi_close($multi);

			return $htmls;
		}

		// SCRIPT BODY

		for ($i=1; $i<100; $i++){
			$urls[] = "http://httpbin.org/get?i=".$i;
		}

		$urls = array_chunk($urls, 5);

		foreach ($urls as $chunk){
			$htmls = multirequest($chunk);
			echo '<pre>';
			var_dump($htmls);
			echo '</pre>';
		}
	}

}
