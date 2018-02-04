<?php

namespace OzSpy\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Exceptions\Crawl\ProductsNotFoundException;
use OzSpy\Jobs\Models\WebProduct\UpdateOrStore;
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
     * @param WebCategoryContract $webCategoryRepo
     * @return mixed
     * @throws ProductsNotFoundException
     */
    public function handle(WebCategoryContract $webCategoryRepo)
    {
        $webCategory = $webCategoryRepo->get(6981);
        $filePath = storage_path('app/scraper/storage/products/' . $webCategory->getKey() . '.json');
        if (file_exists($filePath)) {
            $content = file_get_contents($filePath);
            $scrapingResult = json_decode($content);
            if (!is_null($scrapingResult) && json_last_error() === JSON_ERROR_NONE) {
                if (isset($scrapingResult->category_id) && isset($scrapingResult->scraped_at) && isset($scrapingResult->products)) {
                    $category_id = $scrapingResult->category_id;
                    $last_scraped_at = Carbon::parse($scrapingResult->scraped_at);
                    $products = $scrapingResult->products;
                    if ($webCategory->getKey() == $category_id) {
                        if (count($products) == 0) {
                            throw new ProductsNotFoundException;
                        }

                        foreach ($products as $product) {
                            $productData = (array)$product;
                            dispatch((new UpdateOrStore($webCategory->retailer, $productData, $webCategory))->onConnection('sync'));
                            dump($productData);
                        }

                        $webCategory->last_crawled_products_count = count($products);
                        $webCategory->last_crawled_at = $last_scraped_at;
                        $webCategory->save();
                    }
                }
            }
        }
        dd("called");
    }
}
