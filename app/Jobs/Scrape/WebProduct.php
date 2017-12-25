<?php

namespace OzSpy\Jobs\Scrape;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Models\Base\WebCategory;
use OzSpy\Traits\Commands\Optionable;

class WebProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Optionable;

    protected $webCategory;
    protected $retailer;

    /**
     * Create a new job instance.
     *
     * @param WebCategory $webCategory
     */
    public function __construct(WebCategory $webCategory)
    {
        $this->webCategory = $webCategory;
        $this->retailer = $webCategory->retailer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->webCategory = $this->webCategory->load('recursiveParentCategory');
        $filePath = storage_path('app/scraper/scrapers/' . $this->retailer->abbreviation . '/products.js');
        $execFilePath = storage_path('app/scraper/index.js');
        if (file_exists($filePath)) {
            $options = [
                'category' => "'" . $this->webCategory->toJson() . "'",
                'retailer' => "'" . $this->retailer->toJson() . "'",
                'scraper' => 'products',
            ];

            $optionStr = $this->format($options)->toString()->getOptionsStr();

            exec("node $execFilePath {$optionStr}");
        }
    }
}
