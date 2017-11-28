<?php

namespace OzSpy\Console\Commands\Crawl;

use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Jobs\Crawl\WebProductList as WebProductListJob;

class WebProductList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:web-product-list {--R|retailer=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape product list from retailer websites';

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
     * @param WebCategoryContract $webCategoryRepo
     * @param RetailerContract $retailerRepo
     * @return mixed
     */
    public function handle(WebCategoryContract $webCategoryRepo, RetailerContract $retailerRepo)
    {
        if (is_null($this->option('retailer'))) {
            $webCategories = $webCategoryRepo->all();
        } else {
            $retailer_id = $this->option('retailer');
            $retailer = $retailerRepo->get($retailer_id);
            $webCategories = $retailer->webCategories;
        }
        $this->output->progressStart($webCategories->count());
        foreach ($webCategories as $webCategory) {
//            dispatch((new WebProductListJob($webCategory))->onQueue('crawl-web-product-list')->onConnection('sync'));
            dispatch((new WebProductListJob($webCategory))->onQueue('crawl-web-product-list'));
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        $this->output->success("crawl:web-product-list has dispatched all jobs");
    }
}
