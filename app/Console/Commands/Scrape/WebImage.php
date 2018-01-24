<?php

namespace OzSpy\Console\Commands\Scrape;

use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Models\Base\WebProduct as WebProductModel;
use OzSpy\Jobs\Scrape\WebImage as WebImageJob;

class WebImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:web-image {--R|retailer=} {--C|web-category=} {--P|web-product=} {--active}';

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
     * @param RetailerContract $retailerRepo
     * @param WebCategoryContract $webCategoryRepo
     * @param WebProductContract $webProductRepo
     * @return mixed
     */
    public function handle(RetailerContract $retailerRepo, WebCategoryContract $webCategoryRepo, WebProductContract $webProductRepo)
    {
        $webProducts = collect();
        if (!is_null($this->option('retailer'))) {
            $retailer = $retailerRepo->get($this->option('retailer'));
            if (!is_null($retailer)) {
                $webProducts = $retailer->webProducts;
            }
        }

        if (!is_null($this->option('web-category'))) {
            $webCategory = $webCategoryRepo->get($this->option('web-category'));
            if (!is_null($webCategory)) {
                $webProducts = $webCategory->webProducts;
            }
        }

        if (!is_null($this->option('web-product'))) {
            $webProduct = $webProductRepo->get($this->option('web-product'));
            $webProducts->push($webProduct);
        }

        if ($webProducts->isEmpty()) {
            $webProducts = $webProductRepo->all();
        }

        $this->output->progressStart($webProducts->count());

        $webProducts->each(function (WebProductModel $webProduct) {
            dispatch((new WebImageJob($webProduct))->onQueue('scrape-web-image'));
            $this->output->progressAdvance();
        });

        $this->output->progressFinish();
        $this->output->success("scrape:web-image has dispatched all jobs");
    }
}
