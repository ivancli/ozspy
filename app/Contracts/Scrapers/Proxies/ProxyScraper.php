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

    protected $provider;

    /**
     * fetch all scraped proxies
     * @return array
     */
    public function getProxies()
    {
        return $this->proxies;
    }

    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * fetch content from URL
     * @return void
     */
    abstract public function crawl();

    /**
     * extract IPs and Ports from content
     * @return void
     */
    abstract public function parser();
}