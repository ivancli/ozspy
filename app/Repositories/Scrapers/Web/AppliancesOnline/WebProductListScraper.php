<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 10/12/2017
 * Time: 7:54 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\AppliancesOnline;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper as WebProductListScraperContract;
use OzSpy\Models\Base\WebCategory;
use Symfony\Component\DomCrawler\Crawler;

class WebProductListScraper extends WebProductListScraperContract
{
    const URL = 'https://www.appliancesonline.com.au/angular-api/product-filter.ashx';
    const PRODUCT_API_URL = 'https://www.appliancesonline.com.au/api/product/id/%s';
    /*
     * CategoryID=2715&
     * locationCode=201MAIN&
     * view=show_all&pagenum=2
     * */
    protected $apiUrl;

    /**
     * @var ProxyContract
     */
    protected $proxyRepo;

    /**
     * @var CurlCrawler
     */
    protected $crawler;

    protected $categoryId;

    protected $pageNumber = 0;

    protected $productIds = [];

    public function __construct(WebCategory $webCategory)
    {
        parent::__construct($webCategory);

        $this->crawler = app()->make(CurlCrawler::class);
        $this->crawler = $this->crawler->setHeaders('Content-Type: application/json');
        $this->proxyRepo = app()->make(ProxyContract::class);
        $this->crawler = $this->crawler->setCookiesPath(storage_path('cookie/ao'));
    }

    /**
     * Scrape categories and set to categories attribute
     * @return void
     */
    public function scrape()
    {
        $this->fetchPage();
//        $this->setProxy();
        $this->fetchFromAPI();
    }

    protected function fetchFromAPI()
    {
        $totalPage = null;
        do {
            $this->pageNumber++;
            $this->setUrl();
            $this->crawler = $this->crawler->setJsonResponse();
            if (!is_null($this->apiUrl)) {
                $response = $this->crawler->fetch();
                if ($response->status == 200 && !is_null($response->content)) {
                    $content = $response->content;
                    if (isset($content->pageCount)) {
                        $totalPage = $content->pageCount;
                    }

                    if (isset($content->gridProductIds) && is_array($content->gridProductIds)) {
                        $this->productIds = array_merge($this->productIds, $content->gridProductIds);
                    }
                }
            }
        } while (!is_null($totalPage) && $totalPage > $this->pageNumber - 1);


        foreach ($this->productIds as $productId) {
            $this->apiUrl = sprintf(self::PRODUCT_API_URL, $productId);
            $this->crawler->setURL($this->apiUrl);
            $this->crawler = $this->crawler->setJsonResponse();
            $response = $this->crawler->fetch();
            if ($response->status == 200 && !is_null($response->content)) {
                $content = $response->content;
                if (isset($content->product)) {
                    $product = new \stdClass();
                    $product->name = $content->product->name;
                    $product->brand = isset($content->product->manufacturer) && isset($content->product->manufacturer->name) ? $content->product->manufacturer->name : null;
                    $product->sku = $content->product->sku;
                    $product->retailer_product_id = $content->product->productId;
                    $product->price = floatval($content->product->price) > 0 ? floatval($content->product->price) : null;
                    $product->url = $this->retailer->domain . $content->product->url;
                    $this->products[] = $product;
                }

            }
        }
    }

    /**
     * set URL to crawler
     * @return void
     */
    protected function setUrl()
    {
        if (!is_null($this->categoryId)) {
            $this->apiUrl = self::URL . '?' .
                "CategoryID={$this->categoryId}&" .
                "view=show_all&" .
                "pagenum={$this->pageNumber}";
            $this->crawler->setURL($this->apiUrl);
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

    protected function fetchPage()
    {
        $this->crawler->setCookiesPath(storage_path('cookie/aol'));
        $this->crawler->setURL($this->webCategory->url);
        $response = $this->crawler->fetch();
        if ($response->status == 200 && !is_null($response->content)) {
            $this->extractCategoryId($response->content);
            return $response->content;
        }
        return null;
    }

    protected function extractCategoryId($content)
    {
        $crawler = new Crawler($content);
        $categoryIdNodes = $crawler->filterXPath('//*[@categoryid]');
        if ($categoryIdNodes->count() > 0) {
            $this->categoryId = $categoryIdNodes->first()->attr('categoryid');
        }
    }
}