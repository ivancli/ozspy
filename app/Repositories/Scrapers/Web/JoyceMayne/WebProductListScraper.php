<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 14/12/2017
 * Time: 10:53 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\JoyceMayne;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use Ixudra\Curl\Facades\Curl;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper as WebProductListScraperContract;
use OzSpy\Models\Base\WebCategory;
use Symfony\Component\DomCrawler\Crawler;

class WebProductListScraper extends WebProductListScraperContract
{
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

    /**
     * @var array
     */
    protected $productIds = [];

    /**
     * @var array
     */
    protected $productInfo = [];

    /**
     * @var array
     */
    protected $productLinks = [];


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
//        $this->setProxy();
        $this->fetchProducts();
    }

    protected function fetchProducts()
    {
        $nextPage = $this->webCategory->url;
        while (!is_null($nextPage)) {
            $this->crawler->setURL($nextPage);
            $response = $this->crawler->fetch();
            if ($response->status == 200 && !is_null($response->content)) {
                $content = sanitise_non_utf8($response->content);
                $crawler = new Crawler($content);

                $productNodes = $crawler->filterXPath('//*[@id="category-grid"]//*[contains(@class, "panel_product")]');
                $productNodes->each(function (Crawler $productNode) {
                    $productId = $productNode->attr('data-pid');
                    if (intval($productId) == 0) {
                        return true;
                    }
                    $productName = null;
                    $productUrl = null;
                    $productPrice = null;

                    $productNameNodes = $productNode->filterXPath('//*[@class="info"]//*[contains(@class, "name")]');

                    if ($productNameNodes->count() > 0) {
                        $productNameNode = $productNameNodes->first();
                        $productName = $productNameNode->text();
                        $productUrl = $productNameNode->attr('href');
                    }

                    $productPriceNodes = $productNode->filterXPath('//*[@class="price-device"]//*[@class="price"]/text()');
                    if ($productPriceNodes->count() > 0) {
                        $productPriceNode = $productPriceNodes->first();
                        $productPrice = $productPriceNode->text();
                        $productPrice = floatval($productPrice) > 0 ? floatval($productPrice) : null;
                    }

                    if (!is_null($productName) && !is_null($productUrl)) {
                        $product = new \stdClass();
                        $product->retailer_product_id = $productId;
                        $product->name = html_entity_decode($productName, ENT_QUOTES);
                        $product->url = $productUrl;
                        $product->price = $productPrice;
                        $this->products[] = $product;
                        unset($product);
                    }
                });

                $paginationNodes = $crawler->filterXPath('//*[@id="toolbar-btm"]//*[contains(@class, "icn-next-page")]');

                if ($paginationNodes->count() > 0) {
                    $nextPage = $paginationNodes->first()->attr('href');
                } else {
                    $nextPage = null;
                }
            } else {
                $nextPage = null;
            }
            unset($crawler);
            unset($response);
            unset($productNodes);
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