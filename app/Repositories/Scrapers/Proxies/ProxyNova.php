<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 13/11/2017
 * Time: 11:08 PM
 */

namespace OzSpy\Repositories\Scrapers\Proxies;


use IvanCLI\Crawler\Repositories\CurlCrawler;
use OzSpy\Contracts\Scrapers\Proxies\ProxyScraper;
use Symfony\Component\DomCrawler\Crawler;

class ProxyNova extends ProxyScraper
{
    const URL = 'https://www.proxynova.com/proxy-server-list/country-au/';

    public function __construct()
    {
        $this->crawl();
        $this->parser();
    }

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
     * @return mixed
     */
    protected function parser()
    {
        if (!is_null($this->content)) {
            $domCrawler = new Crawler($this->content);
            $filteredNodes = $domCrawler->filterXPath('//*[@id="tbl_proxy_list"]/tbody/tr');

            $filteredNodes->each(function (Crawler $node) {
                $ipText = $node->text();
                preg_match('#write\(\'(.*?)\'.substr#', $ipText, $ipTextFirstPartMatches);
                preg_match('#\+ \'(.*?)\'\)\;#', $ipText, $ipTextLastPartMatches);
                $firstPartMatch = array_last($ipTextFirstPartMatches);
                $lastPartMatch = array_last($ipTextLastPartMatches);
                $ip = $port = null;
                if (!is_null($firstPartMatch) && !is_null($lastPartMatch)) {
                    $firstPart = substr($firstPartMatch, 2);
                    $ip = $firstPart . $lastPartMatch;
                }
                $portNode = $node->filterXPath('//td[2]/a')->first();
                if (!is_null($portNode)) {
                    $port = $portNode->text();
                }

                if (!is_null($ip) && !is_null($port)) {
                    $this->proxies[] = [
                        'ip' => $ip,
                        'port' => $port,
                    ];
                }
            });
        }
    }
}