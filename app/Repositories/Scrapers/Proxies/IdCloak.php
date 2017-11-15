<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 15/11/2017
 * Time: 11:31 PM
 */

namespace OzSpy\Repositories\Scrapers\Proxies;


use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Scrapers\Proxies\ProxyScraper;
use Symfony\Component\DomCrawler\Crawler;

class IdCloak extends ProxyScraper
{
    const URL = 'http://www.idcloak.com/proxylist/australia-proxy-list.html';

    /**
     * fetch content from URL
     * @return void
     */
    protected function crawl()
    {
        $crawler = new CurlCrawler();
        $crawler->setURL(self::URL);
        $response = $crawler->fetch();
        if ($response->status == 200) {
            $this->content = $response->content;
        }
    }

    /**
     * extract IPs and Ports from content
     * @return void
     */
    protected function parser()
    {
        $crawler = new Crawler($this->content);
        $rowNodes = $crawler->filterXPath('//*[@id="sort"]//tr[td]');
        $rowNodes->each(function (Crawler $rowNode) {
            $ipNode = $rowNode->filterXPath('//td[last()]')->first();
            $portNode = $rowNode->filterXPath('//td[last() - 1]')->first();

            $ip = $ipNode->text();
            $port = $portNode->text();
            
            if (!is_null($ip) && !is_null($port)) {
                $this->proxies[] = [
                    'ip' => trim($ip),
                    'port' => trim($port),
                ];
            }
        });
    }
}