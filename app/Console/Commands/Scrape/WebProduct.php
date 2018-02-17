<?php

namespace OzSpy\Console\Commands\Scrape;

use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;
use OzSpy\Jobs\Scrape\WebProduct as WebProductJob;

class WebProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:web-product {--R|retailer=} {--C|category=} {--active}';

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
     * @param RetailerContract $retailerRepo
     * @param WebCategoryContract $webCategoryRepo
     * @return mixed
     */
    public function handle(RetailerContract $retailerRepo, WebCategoryContract $webCategoryRepo)
    {


        if (!is_null($this->option('retailer'))) {
            $retailer = $retailerRepo->get($this->option('retailer'));
            $this->scrapeSingleRetailer($retailer);
        } elseif (!is_null($this->option('category'))) {
            $webCategory = $webCategoryRepo->get($this->option('category'));
            $this->scrapeSingleCategory($webCategory);
        } else {
            $retailers = $retailerRepo->all();

            $retailers->each(function (Retailer $retailer) {
                $this->scrapeSingleRetailer($retailer);
            });
        }

        $this->output->success("crawl:web-product-list has dispatched all jobs");
    }

    /**
     * Dispatch jobs for a single retailer
     * @param Retailer $retailer
     */
    protected function scrapeSingleRetailer(Retailer $retailer)
    {
        $webCategories = $retailer->webCategories;

        if ($this->option('active') === true) {
            $webCategories = $webCategories->filter(function ($webCategory) {
                return $webCategory->active === true;
            });
        }
        $this->output->comment("Retailer is being processed: {$retailer->name}");

        $this->output->progressStart($webCategories->count());

        $webCategories->each(function (WebCategory $webCategory) use ($retailer) {
            $this->scrapeSingleCategory($webCategory);
            $this->output->progressAdvance();
        });
        $this->output->progressFinish();
        $this->output->comment("Retailer has been processed: {$retailer->name}");
    }

    /**
     * Dispatch jobs for a single web category
     * @param WebCategory $webCategory
     */
    protected function scrapeSingleCategory(WebCategory $webCategory)
    {
        dispatch((new WebProductJob($webCategory))->onQueue('scrape-web-product'));
    }
}
