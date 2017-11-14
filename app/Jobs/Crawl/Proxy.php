<?php

namespace OzSpy\Jobs\Crawl;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Contracts\Scrapers\Proxies\ProxyScraper;

class Proxy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $proxyScraper;

    /**
     * Create a new job instance.
     *
     * @param ProxyScraper $proxyScraper
     */
    public function __construct(ProxyScraper $proxyScraper)
    {
        $this->proxyScraper = $proxyScraper;
    }

    /**
     * Execute the job.
     *
     * @param ProxyContract $proxyContract
     * @return void
     */
    public function handle(ProxyContract $proxyContract)
    {
        $proxies = $this->proxyScraper->getProxies();
        foreach ($proxies as $proxy) {
            $proxyContract->store($proxy);
        }
    }
}
