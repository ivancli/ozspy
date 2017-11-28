<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 28/11/2017
 * Time: 11:41 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\JBHiFi;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper as WebProductListScraperContract;
use OzSpy\Models\Base\WebCategory;
use Symfony\Component\DomCrawler\Crawler;

class WebProductListScraper extends WebProductListScraperContract
{
    const URL = 'https://www.kogan.com/api/v1/products';
    /*?group_variants=false
    &department=phones
    &store=au
    &collection=iphone-se
    &offset=0
    &category=iphone*/
    protected $apiUrl;

    /**
     * @var ProxyContract
     */
    protected $proxyRepo;

    /**
     * @var CurlCrawler
     */
    protected $crawler;

    protected $department;
    protected $filterFieldName;
    protected $filterFieldValue;

    protected $offset = 0;
    protected $hasNext = true;

    public function __construct(WebCategory $webCategory)
    {
        parent::__construct($webCategory);

        $this->crawler = app()->make(CurlCrawler::class);
        $this->crawler->setHeaders('Content-Type: application/json');
        $this->proxyRepo = app()->make(ProxyContract::class);
    }

    /**
     * Scrape categories and set to categories attribute
     * @return void
     */
    public function scrape()
    {
        $this->setProxy();
//        while ($this->hasNext === true) {
//            $this->fetchFromAPI();
//        }
    }

    protected function fetchProductIds()
    {
        $nextPage = $this->webCategory->url;
        while (!is_null($nextPage)) {
            $this->crawler->setURL($nextPage);
            $response = $this->crawler->fetch();
            if ($response->status == 200 && !is_null($response->content)) {
                $crawler = new Crawler($response->content);
                $productIdNodes = $crawler->filterXPath('//*[@data-productId]');
                $productIdNodes->each(function(Crawler $productIdNode){
                    $product_id = $productIdNode->attr('data-productId');
                });

                /*todo append the following query parameters to url and crawl again*/
                /*?p=2&s=releaseDate&sd=2*/
            }
        }
    }

    /**
     * set proxy to crawler
     * @return void
     */
    protected function setProxy()
    {
        $proxy = $this->proxyRepo->random();
        if (!is_null($proxy)) {
            $this->crawler->setProxy($proxy->ip, $proxy->port);
        }
    }
}