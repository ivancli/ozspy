<?php

namespace OzSpy\Console\Commands;

use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Models\Base\WebCategory;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

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
     * @param RetailerContract $retailerContract
     * @return mixed
     */
    public function handle(RetailerContract $retailerContract)
    {
        $retailer = $retailerContract->get(9);
        $retailer->webCategories->each(function (WebCategory $webCategory) use ($retailer) {
            $command = "node " . storage_path('app/scraper/index.js') . " --retailer=" . str_replace(' ', '', $retailer->name) . " --id={$webCategory->getKey()} --url={$webCategory->url} --scraper=products";
            exec($command);
        });
    }
}
