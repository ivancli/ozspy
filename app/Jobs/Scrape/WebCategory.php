<?php

namespace OzSpy\Jobs\Scrape;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Models\Base\Retailer;
use OzSpy\Traits\Commands\Optionable;

class WebCategory implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Optionable;

    protected $retailer;

    /**
     * Create a new job instance.
     *
     * @param Retailer $retailer
     */
    public function __construct(Retailer $retailer)
    {
        $this->retailer = $retailer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filePath = storage_path('app/scraper/scrapers/' . $this->retailer->abbreviation . '/categories.js');
        $execFilePath = storage_path('app/scraper/index.js');
        if (file_exists($filePath)) {
            $options = [
                'retailer' => rawurlencode($this->retailer->toJson()),
                'scraper' => 'categories'
            ];

            $optionStr = $this->format($options)->toString()->getOptionsStr();
            exec("node --expose-gc $execFilePath {$optionStr}");
        }
    }
}
