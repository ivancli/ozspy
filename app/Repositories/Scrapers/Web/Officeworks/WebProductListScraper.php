<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/11/2017
 * Time: 12:12 AM
 */

namespace OzSpy\Repositories\Scrapers\Web\Officeworks;

use Illuminate\Support\Facades\Log;
use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper as WebProductListScraperContract;
use OzSpy\Models\Base\WebCategory;

class WebProductListScraper extends WebProductListScraperContract
{
    protected $urlProperties = [
        'nc' => 'true',
        'contentBeginIndex' => 0,
        'productBeginIndex' => 0,
        'isHistory' => 'false',
        'resultType' => 'both',
        'orderByContent' => null,
        'minPrice' => '0',
        'maxPrice' => '1000000',
        'requesttype' => 'ajax',
        'searchMax' => '100000',
        'searchMin' => '0',
        'cpFilter' => null,
        'scrollPosition' => null,
        'pageSize' => 100,
        'orderBy' => null,
        'pageView' => 'grid',
        'pageType' => 'Category Product List Price',
        'categoryName' => null,
        'beginIndex' => 0,
    ];

    protected $basedUrl;
    protected $apiUrl;
    protected $priceApiUrl = '/webapp/wcs/stores/servlet/OWGetPrice';

    protected $storeId;
    protected $catalogId;
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

    protected $totalPage = null;
    protected $currentPage = null;
    protected $hasNext = true;

    public function __construct(WebCategory $webCategory)
    {
        parent::__construct($webCategory);

        $this->crawler = app()->make(CurlCrawler::class);
        $this->proxyRepo = app()->make(ProxyContract::class);
    }

    /**
     * Scrape categories and set to categories attribute
     * @return void
     */
    public function scrape()
    {
//        $this->setProxy();
        $this->fetchWebPage();
        while ($this->hasNext === true) {
            $this->fetchFromAPI();
        }
        $this->fetchPrices();
    }

    protected function fetchWebPage()
    {
        $this->crawler->setURL($this->webCategory->url);
        $response = $this->crawler->fetch();
        if ($response->status == 200) {
            $content = $response->content;
            preg_match('#ajaxcategoryresultsviewurl = \'(.*?)\';#', $content, $matches);
            if (count($matches) > 1) {
                $match = array_last($matches);
                $this->basedUrl = $this->retailer->domain . $match;
                array_set($this->urlProperties, 'categoryName', $this->webCategory->name);
            }
            unset($matches);
            preg_match('#storeId: \'(.*?)\',#', $content, $matches);
            if (count($matches) > 1) {
                $match = array_last($matches);
                $this->storeId = $match;
            }
            unset($matches);
            preg_match('#catalogId: \'(.*?)\',#', $content, $matches);
            if (count($matches) > 1) {
                $match = array_last($matches);
                $this->catalogId = $match;
            }
        }
    }

    protected function fetchFromAPI()
    {
        $this->setUrl();
        $this->crawler->setJsonResponse();
        if (!is_null($this->apiUrl)) {
            $response = $this->crawler->fetch();
            if ($response->status == 200 && !is_null($response->content)) {
                $content = $response->content;
                if (!is_null($content->totalPages) && !is_null($content->currentPage) && intval($content->totalPages) > intval($content->currentPage)) {
                    $this->hasNext = true;
                    array_set($this->urlProperties, 'beginIndex', array_get($this->urlProperties, 'beginIndex') + intval($content->pageSize));
                } else {
                    $this->hasNext = false;
                }
                if (isset($content->products) && is_array($content->products)) {
                    foreach ($content->products as $product) {
                        $webProduct = new \stdClass();
                        $webProduct->name = html_entity_decode($product->productName, ENT_QUOTES);
//                        $webProduct->slug = $product->slug;
                        $webProduct->url = $this->retailer->domain . $product->productDisplayUrl;
//                        $webProduct->price = !is_null($product->price) && floatval($product->price) > 0 ? $product->price : null;
                        $webProduct->retailer_product_id = $product->uniqueID;
//                        $webProduct->brand = $product->brand;
//                        $webProduct->sku = $product->sku;
                        $webProduct->model = $product->partNumber;
                        $this->products[] = $webProduct;
                    }
                    return;
                }
            }
        }
        $this->hasNext = false;
    }

    protected function fetchPrices()
    {
        $productIds = array_pluck($this->products, 'retailer_product_id');
        $productIds = implode('%2C', $productIds);
        $url = $this->retailer->domain . $this->priceApiUrl . '?'
            . "storeId={$this->storeId}&catalogId={$this->catalogId}&nc=true&productId={$productIds}";
        $this->crawler->setURL($url);
        $this->crawler->setJsonResponse();
        $response = $this->crawler->fetch();
        if ($response->status == 200) {
            $content = $response->content;
            $prices = $content->prices;

            foreach ($this->products as $index => $product) {
                $price = array_first(array_filter($prices, function ($price) use ($product) {
                    return $price->productId == $product->retailer_product_id;
                }));
                if (!is_null($price)) {
                    $this->products[$index]->price = $price->priceBigDecimal;
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
        foreach ($this->urlProperties as $key => $value) {
            $this->apiUrl = $this->basedUrl . "&$key=$value";
        }
        $this->crawler->setURL($this->apiUrl);
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