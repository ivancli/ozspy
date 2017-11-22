<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 23/11/2017
 * Time: 12:12 AM
 */

namespace OzSpy\Repositories\Scrapers\Web\Kogan;

use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper as WebProductListScraperContract;

class WebProductListScraper extends WebProductListScraperContract
{

    /**
     * Scrape categories and set to categories attribute
     * @return void
     */
    public function scrape()
    {
        dd($this->webCategory);
    }
}