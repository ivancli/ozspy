<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 15/11/2017
 * Time: 10:02 PM
 */

namespace OzSpy\Repositories\Scrapers\Proxies;


use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Scrapers\Proxies\ProxyScraper;
use Symfony\Component\DomCrawler\Crawler;

class GatherProxy extends ProxyScraper
{
    const URL = 'http://www.gatherproxy.com/proxylist/country/?c=Australia';

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
        $scriptNodes = $crawler->filterXPath('//*[@id="tblproxy"]/script');
        $scriptNodes->each(function (Crawler $scriptNode) {
            $script = $scriptNode->text();
            preg_match('#"PROXY_IP":"(.*?)"#', $script, $ipMatches);
            preg_match('#"PROXY_PORT":"(.*?)"#', $script, $portMatches);
            $ip = array_last($ipMatches);
            $portText = array_last($portMatches);
            $port = null;
            if (!is_null($portText)) {
                $port = hexdec($portText);
            }
            if (!is_null($ip) && !is_null($port)) {
                $this->proxies[] = [
                    'ip' => trim($ip),
                    'port' => trim($port),
                ];
            }
        });
    }
}