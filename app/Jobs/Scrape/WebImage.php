<?php

namespace OzSpy\Jobs\Scrape;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebProduct as WebProductModel;
use OzSpy\Traits\Commands\Optionable;

class WebImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Optionable;

    /**
     * @var WebProductModel
     */
    protected $webProduct;

    /**
     * @var Retailer
     */
    protected $retailer;

    /**
     * Create a new job instance.
     *
     * @param WebProductModel $webProduct
     */
    public function __construct(WebProductModel $webProduct)
    {
        $this->webProduct = $webProduct;

        $this->retailer = $webProduct->retailer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $filePath = storage_path('app/scraper/scrapers/' . $this->retailer->abbreviation . '/images.js');
        $execFilePath = storage_path('app/scraper/index.js');
        if (file_exists($filePath)) {
            $options = [
                'product' => urlencode($this->webProduct->toJson()),
                'retailer' => urlencode($this->retailer->toJson()),
                'scraper' => 'images',
            ];

            $optionStr = $this->format($options)->toString()->getOptionsStr();

            exec("node $execFilePath {$optionStr}");
        }
    }
}
