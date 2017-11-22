<?php

namespace OzSpy\Jobs\Crawl;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Contracts\Scrapers\Webs\WebProductListScraper;
use OzSpy\Exceptions\ScraperNotFoundException;
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
     * @param WebProductModel $webProductModel
     * @return void
     * @throws ScraperNotFoundException
     */
    public function handle(WebProductContract $webProductRepo, WebProductModel $webProductModel)
    {
        $this->webProductRepo = $webProductRepo;
        $this->webProductModel = $webProductModel;

        $className = 'OzSpy\Repositories\Scrapers\Web\\' . $this->retailer->name . '\WebProductListScraper';

        if (!class_exists($className)) {
            throw new ScraperNotFoundException;
        }

        $this->webProductListScraper = new $className($this->webCategory);

        $this->webProductListScraper->scrape();

        $products = $this->webProductListScraper->getProducts();

        foreach ($products as $product) {
            $this->processSingleProduct($product);
        }
        $this->retailer = $this->retailer->fresh();
        $this->webCategory = $this->webCategory->fresh();
    }

    protected function processSingleProduct($product)
    {

        $productData = (array)$product;
        $productData = $this->__getData($productData);


        if (isset($product->retailer_product_id) && !is_null($product->retailer_product_id)) {
            if ($this->webProductRepo->exist($this->retailer, 'retailer_product_id', $product->retailer_product_id)) {
                $storedProduct = $this->webProductRepo->findBy($this->retailer, 'retailer_product_id', $product->retailer_product_id);
            }
        } elseif (isset($product->slug) && !is_null($product->slug)) {
            if ($this->webProductRepo->exist($this->retailer, 'slug', $product->slug)) {
                $storedProduct = $this->webProductRepo->findBy($this->retailer, 'slug', $product->slug);
            }
        }

        if (!isset($storedProduct)) {
            $storedProduct = $this->webProductRepo->store($productData);
            $this->retailer->webProducts()->save($storedProduct);
        } else {
            $this->webProductRepo->update($storedProduct, $productData);
            $storedProduct = $storedProduct->fresh();
        }
        $this->webCategory->webProducts()->save($storedProduct);

        return $storedProduct;
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webProductModel->getFillable());
    }
}
