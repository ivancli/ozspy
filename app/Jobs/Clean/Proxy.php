<?php

namespace OzSpy\Jobs\Clean;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Models\Crawl\Proxy as ProxyModel;

class Proxy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $proxy;

    /**
     * Create a new job instance.
     *
     * @param ProxyModel $proxy
     */
    public function __construct(ProxyModel $proxy)
    {
        $this->proxy = $proxy;
    }

    /**
     * Execute the job.
     *
     * @param ProxyContract $proxyRepo
     * @return void
     */
    public function handle(ProxyContract $proxyRepo)
    {
        $proxyRepo->test($this->proxy);
        $this->proxy->fresh();
        if ($this->proxy->trashed()) {
            $proxyRepo->delete($this->proxy, true);
        }
    }
}
