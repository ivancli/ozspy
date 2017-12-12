<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 10/12/2017
 * Time: 4:12 PM
 */

namespace OzSpy\Repositories\Scrapers\Web\AppliancesOnline;

use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Webs\WebCategoryScraper as WebCategoryScraperContract;
use OzSpy\Models\Base\Retailer;

class WebCategoryScraper extends WebCategoryScraperContract
{

    const CATEGORIES_XML_URL = 'https://www.appliancesonline.com.au/sitemap-categories.xml';

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
            $content = simplexml_load_string($this->content);
            $listInString = json_encode($content);
            $listInArray = json_decode($listInString);

            if (!is_null($listInArray) && json_last_error() === JSON_ERROR_NONE && isset($listInArray->url)) {
                $urls = $listInArray->url;

                $categoriesGroupedByLevels = [];

                $level = 0;
                while (true) {
                    $categoriesGroupedByLevels[$level] = [];
                    foreach ($urls as $url) {
                        $loc = $url->loc;
                        $paths = array_filter(explode('/', array_get(parse_url($loc), 'path')));
                        if (count($paths) == $level + 1) {
                            $category = new \stdClass();
                            $slug = array_last($paths);
                            $categoryName = str_replace('.aspx', '', str_replace('-', ' ', title_case($slug)));
                            $category->name = html_entity_decode($categoryName, ENT_QUOTES);
                            $category->slug = str_replace('.aspx', '', $slug);
                            $category->url = $loc;
                            $category->categories = [];
                            array_push($categoriesGroupedByLevels[$level], $category);
                        }
                    }
                    if (empty($categoriesGroupedByLevels[$level])) {
                        break;
                    }
                    $level++;
                }

                $categories = [];
                foreach ($categoriesGroupedByLevels as $level) {
                    foreach ($level as $category) {
                        $url = $category->url;
                        $paths = array_filter(explode('/', array_get(parse_url($url), 'path')));
                        $tempCategories = &$categories;
                        foreach ($paths as $path) {
                            if (!array_has($tempCategories, str_replace('.aspx', '', $path)) || !is_object(array_get($tempCategories, str_replace('.aspx', '', $path)))) {
                                $newCategory = new \stdClass();
                                $categoryName = str_replace('.aspx', '', str_replace('-', ' ', title_case($path)));
                                $newCategory->name = html_entity_decode($categoryName, ENT_QUOTES);
                                $newCategory->slug = str_replace('.aspx', '', $path);
                                $newCategory->url = $url;
                                $newCategory->categories = [];
                                array_set($tempCategories, str_replace('.aspx', '', $path), $newCategory);
                            }
                            $tempCategories = &$tempCategories[str_replace('.aspx', '', $path)]->categories;
                        }
                    }
                }
                $this->categories = $categories;
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
        $this->crawler->setURL(self::CATEGORIES_XML_URL);
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