<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 19/11/2017
 * Time: 12:32 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\Kogan;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebCategoryScraper as WebCategoryScraperContract;
use OzSpy\Models\Base\Retailer;

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
            preg_match('#bootstrapHeader \= JSON.parse\(\'(.*?)\'\)\;#', $this->content, $matches);
            if (count($matches) > 1) {
                $categoryJsonEncodedString = array_last($matches);
                //decode Unicode
                $categoryJsonString = json_decode('"' . $categoryJsonEncodedString . '"');
                if (!is_null($categoryJsonString) && json_last_error() === JSON_ERROR_NONE) {
                    $categories = json_decode($categoryJsonString);
                    if (!is_null($categories) && json_last_error() === JSON_ERROR_NONE) {
                        if (isset($categories->departments) && is_array($categories->departments)) {
                            foreach ($categories->departments as $department) {
                                $category = new \stdClass();
                                $category->name = $department->title;
                                $category->slug = $department->slug;
                                $category->url = $this->retailer->domain . $department->href;

                                $category->categories = [];
                                if (isset($department->categories) && is_array($department->categories)) {
                                    foreach ($department->categories as $childCategory) {
                                        $newChildCategory = new \stdClass();
                                        $newChildCategory->name = $childCategory->title;
                                        $newChildCategory->categories = [];
                                        if (isset($childCategory->items) && is_array($childCategory->items)) {
                                            foreach ($childCategory->items as $item) {
                                                $newItem = new \stdClass();
                                                $newItem->name = $item->title;
                                                $newItem->url = $this->retailer->domain . $item->href;
                                                $newChildCategory->categories[] = $newItem;
                                            }
                                        }
                                        $category->categories[] = $newChildCategory;
                                    }
                                }
                                $this->categories[] = $category;
                            }
                        }
                    }
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
        $this->setProxy();
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