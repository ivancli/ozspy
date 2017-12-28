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
                            $productData = $this->__getData($productData);
                            dispatch((new UpdateOrStore($this->retailer, $productData, function (WebProductModel $webProduct) use ($product) {
                                $this->savePrice($webProduct, $product->price);
                                $webProduct->webCategories()->syncWithoutDetaching([$this->webCategory->getKey()]);
                            }))->onConnection('sync'));
                        }

                        $this->retailer = $this->retailer->fresh();
                        $this->webCategory = $this->webCategory->fresh();

                        $this->webCategory->last_crawled_products_count = count($products);
                        $this->webCategory->last_crawled_at = $last_scraped_at;
                        $this->webCategory->save();
                    }
                }
            }
        }
    }

    protected function processSingleProduct($product)
    {
        $productData = (array)$product;
        $productData = $this->__getData($productData);

        if (isset($product->retailer_product_id) && !is_null($product->retailer_product_id)) {
            $storedProduct = $this->webProductRepo->findBy($this->retailer, 'retailer_product_id', $product->retailer_product_id)->first();
        } elseif (isset($product->slug) && !is_null($product->slug)) {
            $storedProduct = $this->webProductRepo->findBy($this->retailer, 'slug', $product->slug)->first();
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

        $this->toBeSyncProductIds[] = $storedProduct->getKey();
//        $this->webCategory->webProducts()->syncWithoutDetaching($storedProduct->getKey());

        unset($productData);

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
        $this->retailer = $this->retailer->fresh('webProducts');
        $retailerWebProducts = $this->retailer->webProducts;

        $pluckedRetailerProductIds = array_unique(array_pluck($data, 'retailer_product_id'));
        $pluckedSlugs = array_unique(array_pluck($data, 'slug'));

        $insertedWebProducts = $retailerWebProducts->filter(function (WebProductModel $retailerWebProduct) use ($pluckedRetailerProductIds, $pluckedSlugs) {
            return
                (!is_null($retailerWebProduct->retailer_product_id) && in_array($retailerWebProduct->retailer_product_id, $pluckedRetailerProductIds))
                ||
                (!is_null($retailerWebProduct->slug) && in_array($retailerWebProduct->slug, $pluckedSlugs));
        });

        $webProductIds = $insertedWebProducts->pluck('id');


//        $this->webCategory->webProducts()->syncWithoutDetaching($webProductIds);
        $this->syncWebProductWebCategoryWithoutDetach($webProductIds->all());

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

            }

            unset($webProduct, $price);
        }
        if (!empty($toBeCreatedPrices)) {
            $this->batchSavePrice($toBeCreatedPrices);
        }

        unset($retailerWebProducts, $pluckedRetailerProductIds, $pluckedSlugs, $webProductIds);
    }

    private function savePrice(WebProductModel $webProduct, $price)
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

    private function syncWebProductWebCategoryWithoutDetach(array $webProductIds)
    {
        if (count($webProductIds) > 0) {
            $query = "INSERT IGNORE INTO web_product_web_category (web_product_id, web_category_id, created_at, updated_at) ";
            $values = [];
            $now = Carbon::now();
            foreach ($webProductIds as $webProductId) {
                $value = '(' . $webProductId . ',' . $this->webCategory->getKey() . ',"' . $now . '", "' . $now . '")';
                $values[] = $value;
            }
            $query .= "VALUES " . join(',', $values);

            \DB::statement($query);
        }
    }
}
