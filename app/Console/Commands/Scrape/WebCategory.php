<?php

namespace OzSpy\Console\Commands\Scrape;

use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Jobs\Scrape\WebCategory as WebCategoryJob;

class WebCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:web-category {--R|retailer=} {--active}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trigger node js web category crawler';

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
            $retailers = $retailers->filter(function ($retailer) {
                return $retailer->getKey() == $this->option('retailer');
            });
        }
        $this->output->progressStart($retailers->count());
        foreach ($retailers as $retailer) {
            if ($this->option('active') !== true || $retailer->active) {
                dispatch((new WebCategoryJob($retailer))->onQueue('scrape-web-category'));
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        $this->output->success("scrape:web-category has dispatched all jobs");
    }
}
