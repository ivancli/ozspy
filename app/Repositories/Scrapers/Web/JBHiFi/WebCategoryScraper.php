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
    const SITE_MAP_URL = 'https://www.jbhifi.com.au/General/Sitemap/';

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
            $crawler = new Crawler(ltrim(utf8_decode($this->content), '?'));
            $categoryListNodes = $crawler->filterXPath('//*[@class="cms-content"]');
            $categoryListNodes->each(function (Crawler $categoryListNode) {
                $this->categories = $this->recursiveFilter($categoryListNode);
            });
        }
    }

    protected function recursiveFilter(Crawler $rootNode)
    {
        $categories = [];
        $categoryULListNodes = $rootNode->children();
        $categoryULListNodes->each(function (Crawler $categoryULListNode) use (&$categories) {
            if ($categoryULListNode->nodeName() == 'ul') {
                $categoryListNodes = $categoryULListNode->children();
                $categoryListNodes->each(function (Crawler $categoryListNode) use (&$categories) {
                    if ($categoryListNode->nodeName() == 'li') {
                        $categoryLinkNodes = $categoryListNode->children();
                        $category = new \stdClass();
                        $categoryLinkNodes->each(function (Crawler $categoryLinkNode) use (&$category, &$categories, $categoryListNode) {
                            if ($categoryLinkNode->nodeName() == 'a') {
                                $category->name = $categoryLinkNode->text();
                                $category->url = $this->retailer->domain . $categoryLinkNode->attr('href');
                                $category->slug = array_last(array_filter(explode('/', $categoryLinkNode->attr('href'))));
                            } elseif ($categoryLinkNode->nodeName() == 'span') {
                                $category->name = $categoryLinkNode->text();
                            } elseif ($categoryLinkNode->nodeName() == 'ul') {
                                $category->categories = $this->recursiveFilter($categoryListNode);
                            }
                        });
                        $categories[] = $category;
                    }
                });

            }
        });
        return $categories;
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
        $this->crawler->setURL(self::SITE_MAP_URL);
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