<?php

namespace OzSpy\Jobs\Update;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebHistoricalPriceContract;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Exceptions\Crawl\ProductsNotFoundException;
use OzSpy\Jobs\Models\WebProduct\UpdateOrStore;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;
use OzSpy\Models\Base\WebProduct as WebProductModel;

class WebProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var \OzSpy\Models\Base\WebCategory
     */
    protected $webCategory;

    /**
     * @var Retailer
     */
    protected $retailer;

    /**
     * @var Collection
     */
    protected $existingWebProducts;

    /**
     * @var array
     */
    protected $toBeCreatedProducts = [];

    /**
     * @var array
     */
    protected $toBeSyncProductIds = [];

    /**
     * @var WebProductContract
     */
    protected $webProductRepo;

    /**
     * @var WebHistoricalPriceContract
     */
    protected $webHistoricalPriceRepo;

    /**
     * @var WebProductModel
     */
    protected $webProductModel;

    /**
     * Create a new job instance.
     *
     * @param \OzSpy\Models\Base\WebCategory $webCategory
     */
    public function __construct(WebCategory $webCategory)
    {
        $this->webCategory = $webCategory;

        $this->retailer = $this->webCategory->retailer;
    }

    /**
     * Execute the job.
     * @param WebProductContract $webProductRepo
     * @param WebHistoricalPriceContract $webHistoricalPriceRepo
     * @param WebProductModel $webProductModel
     * @return void
     * @throws ProductsNotFoundException
     */
    public function handle(WebProductContract $webProductRepo, WebHistoricalPriceContract $webHistoricalPriceRepo, WebProductModel $webProductModel)
    {
        $this->webProductRepo = $webProductRepo;
        $this->webHistoricalPriceRepo = $webHistoricalPriceRepo;
        $this->webProductModel = $webProductModel;

        $filePath = storage_path('app/scraper/storage/products/' . $this->webCategory->getKey() . '.json');
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $scrapingResult = json_decode($content);
            if (!is_null($scrapingResult) && json_last_error() === JSON_ERROR_NONE) {
                if (isset($scrapingResult->category_id) && isset($scrapingResult->scraped_at) && isset($scrapingResult->products)) {
                    $category_id = $scrapingResult->category_id;
                    $last_scraped_at = Carbon::parse($scrapingResult->scraped_at);
                    $products = $scrapingResult->products;
                    if ($this->webCategory->getKey() == $category_id) {
                        if (count($products) == 0) {
                            throw new ProductsNotFoundException;
                        }

                        foreach ($products as $product) {
                            $productData = (array)$product;
                            dispatch((new UpdateOrStore($this->retailer, $productData, $this->webCategory)));
                        }

                        $this->webCategory->last_crawled_products_count = count($products);
                        $this->webCategory->last_crawled_at = $last_scraped_at;
                        $this->webCategory->save();
                    }
                }
            }
        }
    }
}
