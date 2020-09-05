<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Session;

use Goutte\Client;

class CrawlingController extends Controller
{
    public function globusmedicalAllProductsLinks(){
        $page = 1;
        $link_prefix = 'portfolio';
        $products = true;
        while ($products){
            $i = $line = 1;
            $dom_xml = new \DOMDocument();
            @$dom_xml->loadHTMLFile("http://www.globusmedical.com/product-room/page/".$page); // libxml_use_internal_errors(true);
            echo "PAGE: ".$page." ********************************************************** "."<br />";
            foreach($dom_xml->getElementsByTagName('a') as $link) {
                $current = $link->getAttribute('href');
                if (strpos($current, $link_prefix) && $i%2) {
                    echo $line++.": ".$current."<br />";
                    // save current link in DB table
                }
                $i++;
            }
            $page++;
            if($line==1){
                $products = !$products;
            }
        }
    }

    public function globusmedicalGetProductDetails()
    {
        // http://stackoverflow.com/questions/18316093/get-content-of-special-html-tag-by-class-name
        //$url = 'http://www.globusmedical.com/portfolio/acadia/';
        $url = 'http://www.globusmedical.com/portfolio/affirm/';
        $html = file_get_contents($url);
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        $finder = new \DomXPath($doc);
        $node = $finder->query("//div[contains(@class, 'pf-content')]");
        print($doc->saveHTML($node->item(0)));
    }

    public function test(){
        $query = "/html[@class='no-js  csstransforms csstransforms3d csstransitions dj_webkit dj_chrome dj_contentbox']/body[@class='portfolio-template-default single single-portfolio postid-2610']/div[@class='body-wrapper']/div[@class='content-wrapper container wrapper main']/div[@class='page-container container']/div[@class='page-wrapper single-portfolio single-sidebar right-sidebar ']/div[@class='row']/div[@class='gdl-page-left  mb0 eight columns']/div[@class='row']/div[@class='gdl-page-item mb20 gdl-blog-full twelve columns']/div[@class='gdl-single-portfolio ']/div[@class='port-content-wrapper']/div[@class='port-content']/div[@class='pf-content']";
        dd($this->getProducts($query));
    }

    // https://facepunch.com/showthread.php?t=1503733&p=49810310&viewfull=1
    protected function getProducts($query)
    {
        // Loop through all the crawled pages
        foreach($this->getCrawledPages() as $page)
        {
            // Load the DOM and make sure it won't preduce errors
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();
            $dom->loadHTML($page);
            libxml_clear_errors();
            // Find the product container
            $xpath = new \DOMXpath($dom);
            $domProducts = $xpath->query($query);
            // Loop through the products
            foreach($domProducts as $domProduct)
            {
                // Search all the for product information
                $productLink = $this->getLink($xpath, $domProduct);
                $products[] = array(
                    'name' => $this->getName($xpath, $domProduct),
                    'link' => $productLink,
                    'price' => $this->getPrice($xpath, $domProduct),
                );
            }
        }
        return isset($products) ? $products : array();
    }


}
