<?php

namespace OzSpy\Jobs\Crawl;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebHistoricalPriceContract;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper;
use OzSpy\Exceptions\Crawl\ProductsNotFoundException;
use OzSpy\Exceptions\Crawl\ScraperNotFoundException;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;
use OzSpy\Models\Base\WebProduct as WebProductModel;
use OzSpy\Models\Base\WebProduct;

class WebProductList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebCategory
     */
    protected $webCategory;

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
     * @var Retailer
     */
    protected $retailer;

    /**
     * @var WebProductListScraper
     */
    protected $webProductListScraper;

    /**
     * @var array
     */
    protected $toBeCreatedProducts = [];

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
     * @param WebProductContract $webProductRepo
     * @param WebHistoricalPriceContract $webHistoricalPriceRepo
     * @param WebProductModel $webProductModel
     * @return void
     * @throws ProductsNotFoundException
     * @throws ScraperNotFoundException
     */
    public function handle(WebProductContract $webProductRepo, WebHistoricalPriceContract $webHistoricalPriceRepo, WebProductModel $webProductModel)
    {
        $this->webProductRepo = $webProductRepo;
        $this->webHistoricalPriceRepo = $webHistoricalPriceRepo;
        $this->webProductModel = $webProductModel;
        $className = 'OzSpy\Repositories\Scrapers\Web\\' . studly_case($this->retailer->name) . '\WebProductListScraper';

        if (!class_exists($className)) {
            throw new ScraperNotFoundException;
        }
        $this->webProductListScraper = new $className($this->webCategory);
        $this->webProductListScraper->scrape();
        $products = $this->webProductListScraper->getProducts();
        if ($this->webProductListScraper->isAvailable()) {
            if (count($products) == 0) {
                throw new ProductsNotFoundException;
            }

            foreach ($products as $product) {
                $this->processSingleProduct($product);
            }

            $this->processBatchCreate();

            $this->retailer = $this->retailer->fresh();
            $this->webCategory = $this->webCategory->fresh();

            $this->webCategory->last_crawled_products_count = count($products);
            $this->webCategory->last_crawled_at = Carbon::now();
            $this->webCategory->save();
        }
    }

    protected function processSingleProduct($product)
    {

        $productData = (array)$product;
        $productData = $this->__getData($productData);

        if (isset($product->retailer_product_id) && !is_null($product->retailer_product_id)) {
            if ($this->webProductRepo->exist($this->retailer, 'retailer_product_id', $product->retailer_product_id)) {
                $storedProduct = $this->webProductRepo->findBy($this->retailer, 'retailer_product_id', $product->retailer_product_id)->first();
            }
        } elseif (isset($product->slug) && !is_null($product->slug)) {
            if ($this->webProductRepo->exist($this->retailer, 'slug', $product->slug)) {
                $storedProduct = $this->webProductRepo->findBy($this->retailer, 'slug', $product->slug)->first();
            }
        }

        if (!isset($storedProduct)) {
//            $storedProduct = $this->webProductRepo->store($productData);
            /* leave the new products to be saved in batch process */
            $this->toBeCreatedProducts[] = $product;
            return false;
        } else {
            $this->webProductRepo->update($storedProduct, $productData);
            $storedProduct = $storedProduct->fresh();
        }
        $this->retailer->webProducts()->save($storedProduct);

        if (isset($product->price) && !is_null($product->price)) {
            $this->savePrice($storedProduct, $product->price);
        }

        $this->webCategory->webProducts()->syncWithoutDetaching($storedProduct->getKey());

        return $storedProduct;
    }

    protected function processBatchCreate()
    {
        $data = array_map(function ($product) {
            $productData = $this->__getData((array)$product);
            array_set($productData, 'retailer_id', $this->retailer->getKey());
            return $productData;
        }, $this->toBeCreatedProducts);

        $inserted = $this->webProductRepo->insertAll($data);

        foreach ($this->toBeCreatedProducts as $toBeCreatedProduct) {
            if (isset($toBeCreatedProduct->price)) {
                if (isset($toBeCreatedProduct->retailer_product_id) && !is_null($toBeCreatedProduct->retailer_product_id)) {
                    $webProduct = $this->webProductRepo->findBy($this->retailer, 'retailer_product_id', $toBeCreatedProduct->retailer_product_id);
                    $webProduct = $webProduct->first();
                    if (!is_null($webProduct)) {
                        $this->savePrice($webProduct, $toBeCreatedProduct->price);
                    }
                } elseif (isset($toBeCreatedProduct->slug) && !is_null($toBeCreatedProduct->slug)) {
                    $webProduct = $this->webProductRepo->findBy($this->retailer, 'slug', $toBeCreatedProduct->slug);
                    $webProduct = $webProduct->first();
                    if (!is_null($webProduct)) {
                        $this->savePrice($webProduct, $toBeCreatedProduct->price);
                    }
                }
                if (isset($webProduct) && !is_null($webProduct)) {
                    $this->webCategory->webProducts()->syncWithoutDetaching($webProduct->getKey());
                }
            }
        }
    }

    private function savePrice(WebProduct $webProduct, $price)
    {
        $this->webHistoricalPriceRepo->storeIfNull($webProduct, [
            'amount' => $price
        ]);
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webProductModel->getFillable());
    }
}
