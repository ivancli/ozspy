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

    public function __construct()
    {
        $this->crawl();
        $this->parser();
    }

    /**
     * fetch all scraped proxies
     * @return array
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * fetch content from URL
     * @return void
     */
    abstract protected function crawl();

    /**
     * extract IPs and Ports from content
     * @return void
     */
    abstract protected function parser();
}