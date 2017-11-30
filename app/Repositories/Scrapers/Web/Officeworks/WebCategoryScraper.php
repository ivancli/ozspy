<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 30/11/2017
 * Time: 11:37 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\Officeworks;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebCategoryScraper as WebCategoryScraperContract;
use OzSpy\Models\Base\Retailer;

class WebCategoryScraper extends WebCategoryScraperContract
{
    const CATEGORIES_XML_URL = 'https://www.officeworks.com.au/sitemap-categories.xml';

    /**
     * @var ProxyContract
     */
    protected $proxyRepo;

    /**
     * @var CurlCrawler
     */
    protected $crawler;

    /**
     * @var string
     */
    protected $content;

    public function __construct(Retailer $retailer)
    {
        parent::__construct($retailer);

        $this->crawler = app()->make(CurlCrawler::class);
        $this->proxyRepo = app()->make(ProxyContract::class);
    }


    /**
     * Scrape categories and set to categories attribute
     * @return void
     */
    public function scrape()
    {
        $this->crawlEcommerceURL();
        if (!is_null($this->content)) {
            $content = simplexml_load_string($this->content);
            $listInString = json_encode($content);
            $listInArray = json_decode($listInString);

            if (!is_null($listInArray) && json_last_error() === JSON_ERROR_NONE && isset($listInArray->url)) {
                $urls = $listInArray->url;
                foreach ($urls as $url) {
                    
                }
            }
        }
    }

    /**
     * fetch eCommerce home page
     * @return void
     */
    protected function crawlEcommerceURL()
    {
        $this->setUrl();
//        $this->setProxy();
        $response = $this->crawler->fetch();
        if ($response->status == 200) {
            $this->content = $response->content;
        }
    }

    /**
     * set url to crawler
     * @return void
     */
    protected function setUrl()
    {
        $this->crawler->setURL(self::CATEGORIES_XML_URL);
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