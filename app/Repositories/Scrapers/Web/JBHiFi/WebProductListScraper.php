<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 28/11/2017
 * Time: 11:41 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\JBHiFi;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use Ixudra\Curl\Facades\Curl;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper as WebProductListScraperContract;
use OzSpy\Models\Base\WebCategory;
use Symfony\Component\DomCrawler\Crawler;

class WebProductListScraper extends WebProductListScraperContract
{
    const URL = 'https://www.kogan.com/api/v1/products';

    const PRODUCT_INFO_URL = 'https://products.jbhifi.com.au/product/get/id';

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
        $this->setProxy();
        $this->fetchProductIds();
        $this->fetchProductInfo();
        foreach ($this->productInfo as $key => $productInfo) {
            if (array_has($this->productLinks, $productInfo->ProductID)) {
                $product = new \stdClass();
                $product->retailer_product_id = $productInfo->ProductID;
                $product->name = $productInfo->DisplayName;
                $product->sku = $productInfo->SKU;
                $product->brand = $productInfo->Brand;
                $product->url = $this->retailer->domain . array_get($this->productLinks, $productInfo->ProductID);
                $product->price = $productInfo->PlacedPrice;
                $this->products[] = $product;
            }
        }
    }

    protected function fetchProductIds()
    {
        $nextPage = $this->webCategory->url;
        $pageNumber = 1;

        while (!is_null($nextPage)) {
            $this->crawler->setURL($nextPage);
            $response = $this->crawler->fetch();
            if ($response->status == 200 && !is_null($response->content)) {
                $crawler = new Crawler($response->content);

                $productNodes = $crawler->filterXPath('//*[@data-productid!="{{ hit.Id }}"]');
                $productNodes->each(function (Crawler $productNode) {
                    $productId = $productNode->attr('data-productid');
                    if (intval($productId) == 0) {
                        return true;
                    }
                    $this->productIds[] = $productId;

                    $linkNode = $productNode->filterXPath('//a')->first();
                    $productLink = $linkNode->attr('href');
                    $this->productLinks[$productId] = $productLink;
                });

                if ($productNodes->count() > 0) {
                    $pageNumber++;
                    $nextPage = $this->webCategory->url . "?p={$pageNumber}";
                } else {
                    $nextPage = null;
                }
            }
            unset($crawler);
            unset($response);
            unset($productNodes);
        }
    }

    protected function fetchProductInfo()
    {
        $response = Curl::to(self::PRODUCT_INFO_URL)
            ->withData([
                'Ids' => $this->productIds
            ])
            ->returnResponseObject()
            ->asJsonRequest()
            ->post();
        if ($response->status == 200 && !is_null($response->content)) {
            $productInfo = json_decode(ltrim(utf8_decode($response->content), '?'));
            if (!is_null($productInfo) && json_last_error() == JSON_ERROR_NONE) {
                if (isset($productInfo->Result) && isset($productInfo->Result->Products)) {
                    $this->productInfo = $productInfo->Result->Products;
                }
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