<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 22/11/2017
 * Time: 10:41 PM
 */

namespace OzSpy\Contracts\Scrapers\Webs;


use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;

abstract class WebProductListScraper
{
    /**
     * A list of categories
     * @var array
     */
    protected $products = [];

    /**
     * @var WebCategory
     */
    protected $webCategory;

    /**
     * @var Retailer
     */
    protected $retailer;

    /**
     * @var bool
     */
    protected $available = true;

    public function __construct(WebCategory $webCategory)
    {
        $this->webCategory = $webCategory;
        $this->retailer = $webCategory->retailer;
    }

    /**
     * Scrape categories and set to categories attribute
     * @return void
     */
    public abstract function scrape();

    /**
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function isAvailable()
    {
        return $this->available;
    }
}