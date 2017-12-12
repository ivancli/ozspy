<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 13/12/2017
 * Time: 1:01 AM
 */

namespace OzSpy\Repositories\Scrapers\Web\WinningAppliances;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper as WebProductListScraperContract;
use OzSpy\Models\Base\WebCategory;

class WebProductListScraper extends WebProductListScraperContract
{
    const URL = 'https://www.winningappliances.com.au/api/category/%s';
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
        $this->fetchFromAPI();
    }

    protected function fetchFromAPI()
    {
        $this->setUrl();
        $this->crawler->setJsonResponse();
        if (!is_null($this->apiUrl)) {
            $response = $this->crawler->fetch();
            if ($response->status == 200 && !is_null($response->content)) {
                if (isset($response->content->products) && is_array($response->content->products)) {
                    foreach ($response->content->products as $product) {
                        $slug = array_last(array_filter(explode('/', $product->uri)));
                        $webProduct = new \stdClass();
                        $webProduct->name = html_entity_decode($product->title, ENT_QUOTES);
                        $webProduct->slug = $slug;
                        $webProduct->url = $this->retailer->domain . $product->uri;
                        $webProduct->price = !is_null($product->price) && floatval($product->price) > 0 ? $product->price : null;
                        $webProduct->brand = $product->brand;
                        $webProduct->sku = $product->sku;
                        $this->products[] = $webProduct;
                    }
                    return;
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
        $path = $this->composerUrlCategoryPaths($this->webCategory);
        $this->apiUrl = sprintf(self::URL, urlencode($path));
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

    /**
     * @param WebCategory $webCategory
     * @return string
     */
    protected function composerUrlCategoryPaths(WebCategory $webCategory)
    {
        $path = "";
        if (!is_null($webCategory->parentCategory)) {
            $parentPaths = $this->composerUrlCategoryPaths($webCategory->parentCategory);
            if (!empty($parentPaths)) {
                $path = $parentPaths . "/";
            }
        }
        $path .= $webCategory->slug;
        return $path;
    }
}