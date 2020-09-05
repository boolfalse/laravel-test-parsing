<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Session;
use Goutte\Client;

class Controller extends BaseController
{
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
    // ? Crawler
    public function crawl12(){
        $url = 'http://www.globusmedical.com/product-room/';
        $crawler = new Crawler();
        $crawler->addHtmlContent($url);
        $crawler->filterXPath('//a[contains(@href, "portfolio")]')->evaluate('@href');
        dd($crawler);
    }
// ----------------------------------------------------------------------- 20022017
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


// *************************************************************************************


    public function xpathRegexQuery(){
        libxml_use_internal_errors(true); // Supress XML parsing errors (this is needed to parse Wikipedia's XHTML)
        $domDoc = new DOMDocument();
        $domDoc->load('http://en.wikipedia.org/wiki/PHP'); // Load the PHP Wikipedia article
        $xPath = new DOMXPath($domDoc);
        $xPath->registerNamespace('html', 'http://www.w3.org/1999/xhtml'); // Create XPath object and register the XHTML namespace
        $xPath->registerNamespace('php', 'http://php.net/xpath'); // Register the PHP namespace if you want to call PHP functions
        $xPath->registerPhpFunctions('preg_match'); // Register preg_match to be available in XPath queries // // You can also pass an array to register multiple functions, or call registerPhpFunctions() with no parameters to register all PHP functions
        $regex = '@^http://[^/]+(?<!wikipedia.org)/@';
        $links = $xPath->query("//html:a[ php:functionString('preg_match', '$regex', @href) > 0 ]");  // Find all external links in the article

        // Print out matched entries
        echo "Found " . (int) $links->length . " external linksnn";
        foreach($links as $linkDom) { /* @var $entry DOMElement */
            $link = simplexml_import_dom($linkDom);
            $desc = (string) $link;
            $href = (string) $link['href'];

            echo " - ";
            if ($desc && $desc != $href) {
                echo "$desc: ";
            }
            echo "$href\n";
        }
    }

}



