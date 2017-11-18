<?php

namespace OzSpy\Console\Commands\Clean;

use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Models\Crawl\Proxy as ProxyModel;
use OzSpy\Jobs\Clean\Proxy as ProxyJob;

class Proxy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:proxy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test and remove inactive proxies';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param ProxyContract $proxyRepo
     * @return void
     */
    public function handle(ProxyContract $proxyRepo)
    {
        $proxies = $proxyRepo->all(true);
        $this->output->createProgressBar($proxies->count());
        $this->output->progressStart();
        $proxies->each(function (ProxyModel $proxy) {
            dispatch((new ProxyJob($proxy))->onQueue('clean-proxy')->onConnection('sync'));
            $this->output->progressAdvance();
        });
        $this->output->progressFinish();
        $this->output->success("clean:proxy has dispatched all jobs");
    }
}
