<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 9/12/2017
 * Time: 7:22 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\TheGoodGuys;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use Ixudra\Curl\Facades\Curl;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper as WebProductListScraperContract;
use OzSpy\Models\Base\WebCategory;
use Symfony\Component\DomCrawler\Crawler;

class WebProductListScraper extends WebProductListScraperContract
{
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

    protected $hasNext = true;

    /**
     * @var array
     */
    protected $productLinks = [];

    protected $nextUrl = null;

    protected $sessionId = null;

    protected $index = 0;

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
        $content = $this->fetchPage();
        while (!is_null($content)) {
            $crawler = new Crawler($content);

            $productNodes = $crawler->filterXPath('//*[@id="product_listing_tab"]/ul/li');
            $productNodes->each(function (Crawler $productNode) {
                $productId = null;
                $productName = null;
                $productBrand = null;
                $productUrl = null;
                $productModel = null;
                $productPrice = null;

                $productScriptNodes = $productNode->filterXPath('//*[contains(@class, "product-tile-inner")]//script');
                if ($productScriptNodes->count() > 0) {
                    $productScriptNode = $productScriptNodes->first();

                    $scriptText = $productScriptNode->text();

                    preg_match('#\'id\': \'(.*?)\',#', $scriptText, $idMatches);
                    preg_match('#\'name\': \'(.*?)\',#', $scriptText, $nameMatches);
                    preg_match('#\'price\': \'(.*?)\',#', $scriptText, $priceMatches);
                    preg_match('#\'brand\': \'(.*?)\',#', $scriptText, $brandMatches);
                    $productId = array_last($idMatches);
                    $productName = array_last($nameMatches);
                    $productBrand = array_last($brandMatches);
                    $productPrice = array_last($priceMatches);
                }

                $productModelNodes = $productNode->filterXPath('//*[contains(@class, "product-tile-model")]/text()');
                if ($productModelNodes->count() > 0) {
                    $productModel = $productModelNodes->first()->text();
                }

                $productUrlNodes = $productNode->filterXPath('//*[@class="disp-block"]');
                if ($productUrlNodes->count() > 0) {
                    $productUrl = $productUrlNodes->first()->attr('href');
                }

                if (!is_null($productId) && !is_null($productName) && !is_null($productUrl)) {
                    $product = new \stdClass();
                    $product->retailer_product_id = $productId;
                    $product->name = html_entity_decode($productName, ENT_QUOTES);
                    $product->brand = $productBrand;
                    $product->model = $productModel;
                    $product->url = $productUrl;
                    $product->price = floatval($productPrice) > 0 ? floatval($productPrice) : null;
                    $this->products[] = $product;
                    $this->index++;
                    unset($product);
                }
            });

            $paginationNodes = $crawler->filterXPath('//*[@id="WC_SearchBasedNavigationResults_pagination_link_right_categoryResults"]');

            if ($paginationNodes->count() > 0) {
                $content = $this->fetchProductList();
            } else {
                $content = null;
            }
            unset($crawler);
            unset($response);
            unset($productNodes);
        }
    }

    protected function fetchPage()
    {
        $this->crawler->setCookiesPath(storage_path('cookie/tgg'));
        $this->crawler->setURL($this->webCategory->url);
        $response = $this->crawler->fetch();
        if ($response->status == 200 && !is_null($response->content)) {
            $this->extractNextUrl($response->content);
            return $response->content;
        }
        return null;
    }

    protected function fetchProductList()
    {
        if (!is_null($this->nextUrl)) {
            $parts = parse_url($this->nextUrl);
            parse_str(array_get($parts, 'query'), $query);

            $sessionId = array_get($query, 'ddkey');
            if (!is_null($sessionId)) {
                $this->sessionId = str_replace('ProductListingView', '', $sessionId);
            }

            $response = Curl::to($this->nextUrl)
                ->withHeaders([
                    'Accept-Language: en-us',
                    'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.15) Gecko/20110303 Firefox/3.6.15',
                    'Connection: Keep-Alive',
                    'Cache-Control: no-cache',
                ])
                ->setCookieFile(storage_path('cookie/tgg'))
                ->setCookieJar(storage_path('cookie/tgg'))
                ->returnResponseObject()
                ->withData([
                    "contentBeginIndex" => "0",
                    "productBeginIndex" => $this->index,
                    "beginIndex" => $this->index,
                    "orderBy" => "",
                    "facetId" => "",
                    "pageView" => "grid",
                    "resultType" => "products",
                    "orderByContent" => "",
                    "searchTerm" => "",
                    "facet" => "",
                    "facetLimit" => "",
                    "minPrice" => "",
                    "maxPrice" => "",
                    "pageSize" => "",
                    "storeId" => array_get($query, 'storeId'),
                    "catalogId" => array_get($query, 'catalogId'),
                    "langId" => "-1",
                    "objectId" => $this->sessionId,
                    "requesttype" => "ajax",
                ])
                ->post();
            if ($response->status == 200) {
                $this->extractNextUrl($response->content);
                return $response->content;
            }
        }
        return null;
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

    private function extractNextUrl($content)
    {
        preg_match('#SearchBasedNavigationDisplayJS.init\(\'(?:.*?)\',\'(.*?)\'\)#', $content, $matches);
        $this->nextUrl = array_last($matches);
    }
}