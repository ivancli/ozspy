<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 14/12/2017
 * Time: 10:27 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\JoyceMayne;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebCategoryScraper as WebCategoryScraperContract;
use OzSpy\Models\Base\Retailer;
use Symfony\Component\DomCrawler\Crawler;

class WebCategoryScraper extends WebCategoryScraperContract
{
    const SITE_MAP_URL = 'https://www.joycemayne.com.au/sitemap';
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
            $crawler = new Crawler($this->content);
            $categoryNodes = $crawler->filterXPath('//*[@id="sitemap"]//a');
            $categoryNodes->each(function (Crawler $categoryNode) {

                $category = new \stdClass();
                $category->name = html_entity_decode($categoryNode->text(), ENT_QUOTES);
                $category->url = $categoryNode->attr('href');
                $category->slug = str_slug($category->name);
                $this->categories[] = $category;
            });
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
        $this->crawler->setURL(self::SITE_MAP_URL);
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