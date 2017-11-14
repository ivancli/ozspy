<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 13/11/2017
 * Time: 10:50 PM
 */

namespace OzSpy\Contracts\Scrapers\Proxies;


abstract class ProxyScraper
{
    protected $content;

    protected $proxies = [];

    /**
     * fetch all scraped proxies
     * @return array
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    protected function test()
    {

    }

    /**
     * fetch content from URL
     * @return void
     */
    abstract protected function crawl();

    /**
     * extract IPs and Ports from content
     * @return mixed
     */
    abstract protected function parser();
}