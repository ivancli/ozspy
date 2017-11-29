<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 28/11/2017
 * Time: 9:22 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\JBHiFi;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use IvanCLI\Crawler\Repositories\EntranceCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebCategoryScraper as WebCategoryScraperContract;
use OzSpy\Models\Base\Retailer;
use Symfony\Component\DomCrawler\Crawler;

class WebCategoryScraper extends WebCategoryScraperContract
{
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

        $this->crawler = app()->make(EntranceCrawler::class);
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
            $departmentNodes = $crawler->filterXPath('//*[@class="brands"]');
            $departmentNodes->each(function (Crawler $departmentNode) {
                $departmentInfoNodes = $departmentNode->filterXPath('//h3/a');
                if ($departmentInfoNodes->count() > 0) {
                    $departmentInfoNode = $departmentInfoNodes->first();

                    $category = new \stdClass();
                    $category->name = $departmentInfoNode->text();
                    $category->slug = array_last(explode('/', $departmentInfoNode->attr('href')));
                    $category->url = $this->retailer->domain . $departmentInfoNode->attr('href');
                    $category->categories = [];

                    $this->crawler->setURL($category->url);
                    $subCategoryResponse = $this->crawler->fetch();
                    if ($subCategoryResponse->status == 200 && !is_null($subCategoryResponse->content)) {
                        $subCategoryCrawler = new Crawler($subCategoryResponse->content);
                        $subCategoryNodes = $subCategoryCrawler->filterXPath('//*[contains(@class, "category-menu")]/li[not(@class)]');
                        $subCategoryNodes->each(function (Crawler $subCategoryNode) use (&$category) {
                            $subCategoryLinkNodes = $subCategoryNode->filterXPath('//a');
                            if ($subCategoryLinkNodes->count() > 0) {
                                $subCategoryLinkNode = $subCategoryLinkNodes->first();
                                $subCategory = new \stdClass();
                                $subCategory->name = $subCategoryLinkNode->children()->first()->text();
                                $subCategory->url = $this->retailer->domain . $subCategoryLinkNode->attr('href');
                                $category->categories[] = $subCategory;
                            }
                        });
                    }
                    $this->categories[] = $category;
                }
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
        $this->crawler->setURL($this->retailer->ecommerce_url);
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