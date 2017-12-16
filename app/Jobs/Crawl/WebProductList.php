<?php

namespace OzSpy\Jobs\Crawl;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
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
     * @var Collection
     */
    protected $existingWebProducts;

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

        $this->existingWebProducts = $webCategory->webProducts;
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
            $storedProduct = $this->existingWebProducts->filter(function ($existingWebProduct) use ($product) {
                return $existingWebProduct->retailer_product_id == $product->retailer_product_id;
            })->first();
        } elseif (isset($product->slug) && !is_null($product->slug)) {
            $storedProduct = $this->existingWebProducts->filter(function ($existingWebProduct) use ($product) {
                return $existingWebProduct->slug == $product->slug;
            })->first();
        }

        if (!isset($storedProduct) || is_null($storedProduct)) {
            /* leave the new products to be saved in batch process */
            /* check array existence */
            $exist = true;
            if (isset($product->retailer_product_id) && !is_null($product->retailer_product_id)) {
                $exist = count(array_filter($this->toBeCreatedProducts, function ($toBeCreatedProduct) use ($product) {
                        return $toBeCreatedProduct->retailer_product_id == $product->retailer_product_id;
                    })) > 0;
            } elseif (isset($product->slug) && !is_null($product->slug)) {
                $exist = count(array_filter($this->toBeCreatedProducts, function ($toBeCreatedProduct) use ($product) {
                        return $toBeCreatedProduct->slug == $product->slug;
                    })) > 0;
            }
            if ($exist === false) {
                $this->toBeCreatedProducts[] = $product;
            }
            return false;
        } else {
            $this->webProductRepo->update($storedProduct, $productData);
            $storedProduct = $storedProduct->fresh();
        }

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

        $this->webProductRepo->insertAll($data);
        $retailerWebProducts = $this->retailer->webProducts;

        $pluckedRetailerProductIds = array_unique(array_pluck($data, 'retailer_product_id'));
        $pluckedSlugs = array_unique(array_pluck($data, 'slug'));

        $insertedWebProducts = $retailerWebProducts->filter(function (WebProduct $retailerWebProduct) use ($pluckedRetailerProductIds, $pluckedSlugs) {
            return
                (!is_null($retailerWebProduct->retailer_product_id) && in_array($retailerWebProduct->retailer_product_id, $pluckedRetailerProductIds))
                ||
                (!is_null($retailerWebProduct->slug) && in_array($retailerWebProduct->slug, $pluckedSlugs));
        });

        $webProductIds = $insertedWebProducts->pluck('id');

        $this->webCategory->webProducts()->syncWithoutDetaching($webProductIds);

        $toBeCreatedPrices = [];

        foreach ($this->toBeCreatedProducts as $toBeCreatedProduct) {
            if (isset($toBeCreatedProduct->price) && !is_null($toBeCreatedProduct->price)) {
                if (isset($toBeCreatedProduct->retailer_product_id) && !is_null($toBeCreatedProduct->retailer_product_id)) {
                    $webProduct = $insertedWebProducts->filter(function ($insertedWebProduct) use ($toBeCreatedProduct) {
                        return $insertedWebProduct->retailer_product_id == $toBeCreatedProduct->retailer_product_id;
                    })->first();
                } elseif (isset($toBeCreatedProduct->slug) && !is_null($toBeCreatedProduct->slug)) {
                    $webProduct = $insertedWebProducts->filter(function ($insertedWebProduct) use ($toBeCreatedProduct) {
                        return $insertedWebProduct->slug == $toBeCreatedProduct->slug;
                    })->first();
                }

                if (isset($webProduct) && !is_null($webProduct)) {
                    $price = [
                        'web_product_id' => $webProduct->getKey(),
                        'amount' => $toBeCreatedProduct->price,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                    $toBeCreatedPrices[] = $price;
                }
                unset($price);
            }
            unset($webProduct);
        }
        if (!empty($toBeCreatedPrices)) {
            $this->batchSavePrice($toBeCreatedPrices);
        }
    }

    private function savePrice(WebProduct $webProduct, $price)
    {
        $this->webHistoricalPriceRepo->storeIfNull($webProduct, [
            'amount' => $price
        ]);
    }

    private function batchSavePrice(array $prices)
    {
        $this->webHistoricalPriceRepo->insertAll($prices);
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webProductModel->getFillable());
    }
}
