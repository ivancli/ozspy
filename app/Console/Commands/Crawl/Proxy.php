<?php

namespace OzSpy\Console\Commands\Crawl;

use Illuminate\Console\Command;
use OzSpy\Jobs\Crawl\Proxy as CrawlProxy;

class Proxy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:proxy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $proxyScrapersPath = app_path('Repositories/Scrapers/Proxies');
        $proxyScrapers = list_files_with_directories($proxyScrapersPath, true);
        foreach ($proxyScrapers as $proxyScraper) {
            $proxyScraperInstance = app()->make("OzSpy\Repositories\Scrapers\Proxies\\$proxyScraper");
            dispatch((new CrawlProxy($proxyScraperInstance))->onQueue('crawl-proxy')->onConnection('sync'));
        }
    }
}
