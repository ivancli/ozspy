<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/11/2017
 * Time: 12:12 AM
 */

namespace OzSpy\Repositories\Scrapers\Web\DickSmith;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper as WebProductListScraperContract;
use OzSpy\Models\Base\WebCategory;

class WebProductListScraper extends WebProductListScraperContract
{
    const URL = 'https://www.dicksmith.com.au/api/v1/products';
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
        $this->setupSlugs();
        $this->setProxy();
        while ($this->hasNext === true) {
            $this->fetchFromAPI();
        }
    }

    protected function fetchFromAPI()
    {
        $this->setUrl();
        $this->crawler->setJsonResponse();
        if (!is_null($this->apiUrl)) {
            $response = $this->crawler->fetch();
            if ($response->status == 200 && !is_null($response->content)) {
                if (isset($response->content->meta)) {
                    $meta = $response->content->meta;
                    $this->hasNext = $meta->has_next;
                    $this->offset += $meta->limit;
                }
                if (isset($response->content->objects) && is_array($response->content->objects)) {
                    foreach ($response->content->objects as $product) {
                        $webProduct = new \stdClass();
                        $webProduct->name = $product->title;
                        $webProduct->slug = $product->slug;
                        $webProduct->url = $this->retailer->domain . $product->url;
                        $webProduct->price = !is_null($product->price) && floatval($product->price) > 0 ? $product->price : null;
                        $webProduct->retailer_product_id = $product->id;
                        $webProduct->brand = $product->brand;
                        $webProduct->sku = $product->sku;
                        $this->products[] = $webProduct;
                    }
                    return;
                }
            }
        }
        $this->hasNext = false;
    }

    /**
     * set URL to crawler
     * @return void
     */
    protected function setUrl()
    {
        if (!is_null($this->department)) {
            $this->apiUrl = self::URL . '?' .
                'group_variants=false&' .
                'store=da&' .
                (!is_null($this->department) ? "department={$this->department}&" : null) .
                (!is_null($this->filterFieldName) && !is_null($this->filterFieldValue) ? "{$this->filterFieldName}={$this->filterFieldValue}&" : null) .
                "offset={$this->offset}";
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

    /**
     * @return void
     */
    protected function setupSlugs()
    {
        if (!is_null($this->webCategory->parentCategory)) {
            $parentCategory = $this->webCategory->parentCategory;
            if (!is_null($parentCategory->parentCategory)) {
                $this->department = $parentCategory->parentCategory->slug;
                $this->filterFieldName = $parentCategory->field;
                if ($parentCategory->field != 'category' && $parentCategory->field != 'collection') {
                    $this->filterFieldValue = urlencode($this->webCategory->name);
                } else {
                    $this->filterFieldValue = urlencode($this->webCategory->slug);
                }
            } else {
                $this->available = false;
                return;
            }
        } else {
            $this->department = $this->webCategory->slug;
        }
    }
}