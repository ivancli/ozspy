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
     * @param ProxyContract $proxyRepo
     * @return void
     */
    public function handle(ProxyContract $proxyRepo)
    {
        $this->proxyScraper->crawl();
        $this->proxyScraper->parser();
        $proxies = $this->proxyScraper->getProxies();
        $provider = $this->proxyScraper->getProvider();
        foreach ($proxies as $proxy) {
            $data = array_merge($proxy, ['provider' => $provider]);
            $proxy = $proxyRepo->store($data);
            if (!is_null($proxy)) {
                $proxyRepo->test($proxy);
            }
        }
    }
}
