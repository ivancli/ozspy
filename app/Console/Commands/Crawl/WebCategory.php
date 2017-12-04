<?php

namespace OzSpy\Console\Commands\Crawl;

use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Jobs\Crawl\WebCategory as WebCategoryJob;

class WebCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:web-category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape categories from retailer websites';

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
     * @param RetailerContract $retailerRepo
     * @return void
     */
    public function handle(RetailerContract $retailerRepo)
    {
        $retailers = $retailerRepo->all();
        $this->output->progressStart($retailers->count());
        foreach ($retailers as $retailer) {
            if ($retailer->active) {
                dispatch((new WebCategoryJob($retailer))->onQueue('crawl-web-category'));
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        $this->output->success("crawl:web-category has dispatched all jobs");
    }
}
