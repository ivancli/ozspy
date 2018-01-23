<?php

namespace OzSpy\Console\Commands\Update;

use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Jobs\Update\WebCategory as WebCategoryJob;

class WebCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:web-category {--R|retailer=} {--active}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch jobs to create/update categories based on given scraping result';

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
                dispatch((new WebCategoryJob($retailer))->onQueue('update-web-category'));
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        $this->output->success("update:web-category has dispatched all jobs");
    }
}
