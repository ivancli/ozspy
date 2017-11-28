<?php

namespace OzSpy\Jobs\Crawl;

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
            $this->retailer = $this->retailer->fresh();
            $this->webCategory = $this->webCategory->fresh();
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
            $storedProduct = $this->webProductRepo->store($productData);
            $this->retailer->webProducts()->save($storedProduct);
        } else {
            $this->webProductRepo->update($storedProduct, $productData);
            $storedProduct = $storedProduct->fresh();
        }

        /*TODO store price*/
        if (isset($product->price) && !is_null($product->price)) {
            $this->webHistoricalPriceRepo->storeIfNull($storedProduct, [
                'amount' => $product->price
            ]);
        }


        $this->webCategory->webProducts()->syncWithoutDetaching($storedProduct->getKey());

        /*TODO need to tidy up category product associations*/

        return $storedProduct;
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webProductModel->getFillable());
    }
}
