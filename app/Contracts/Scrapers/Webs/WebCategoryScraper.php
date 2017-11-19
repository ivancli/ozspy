<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 16/11/2017
 * Time: 11:03 PM
 */

namespace OzSpy\Contracts\Scrapers\Webs;


use OzSpy\Models\Base\Retailer;

abstract class WebCategoryScraper
{
    /**
     * A list of categories
     * @var array
     */
    protected $categories = [];

    /**
     * @var Retailer
     */
    protected $retailer;

    public function __construct(Retailer $retailer)
    {
        $this->retailer = $retailer;
    }

    /**
     * Scrape categories and set to categories attribute
     * @return void
     */
    public abstract function scrape();

    /**
     * @return array
     */
    public function getCategories()
    {
        return $this->categories;
    }
}