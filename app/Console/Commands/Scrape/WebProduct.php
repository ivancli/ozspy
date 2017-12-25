<?php

namespace OzSpy\Console\Commands\Scrape;

use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Models\Base\Retailer;
use OzSpy\Jobs\Scrape\WebProduct as WebProductJob;

class WebProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:web-product {--R|retailer=} {--active}';

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
     * @return mixed
     */
    public function handle(RetailerContract $retailerRepo)
    {
        $retailers = $retailerRepo->all();

        if (!is_null($this->option('retailer'))) {
            $retailers = $retailers->filter(function (Retailer $retailer) {
                return $retailer->getKey() == $this->option('retailer');
            });
        }

        $retailers->each(function (Retailer $retailer) {
            $webCategories = $retailer->webCategories;

            if ($this->option('active') === true) {
                $webCategories = $webCategories->filter(function ($webCategory) {
                    return $webCategory->active === true;
                });
            }
            $this->output->comment("Retailer is being processed: {$retailer->name}");

            $this->output->progressStart($webCategories->count());

            $webCategories->each(function (\OzSpy\Models\Base\WebCategory $webCategory) use ($retailer) {
                dispatch((new WebProductJob($webCategory))->onQueue('scrape-web-product'));
                $this->output->progressAdvance();
            });
            $this->output->progressFinish();
            $this->output->comment("Retailer has been processed: {$retailer->name}");
        });

        $this->output->success("crawl:web-product-list has dispatched all jobs");
    }
}
