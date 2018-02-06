<?php

namespace OzSpy\Jobs\Models\WebProduct;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use OzSpy\Contracts\Models\Base\WebHistoricalPriceContract;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebCategory;
use OzSpy\Models\Base\WebProduct;

/**
 * Update existing WebProduct or Store a new WebProduct
 * Class UpdateOrStore
 * @package OzSpy\Jobs\Models\WebProduct
 */
class UpdateOrStore implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var WebProduct
     */
    protected $webProductModel;

    /**
     * @var Retailer
     */
    protected $retailer;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $productData;

    /**
     * @var WebCategory
     */
    protected $webCategory;

    /**
     * @var WebHistoricalPriceContract
     */
    protected $webHistoricalPriceRepo;

    /**
     * UpdateOrStore constructor.
     * @param Retailer $retailer
     * @param array $data
     * @param WebCategory|null $webCategory
     */
    public function __construct(Retailer $retailer, array $data, WebCategory $webCategory = null)
    {
        $this->retailer = $retailer;

        $this->data = $data;

        $this->webCategory = $webCategory;
    }

    /**
     * Execute the job.
     *
     * @param WebProductContract $webProductRepo
     * @param WebHistoricalPriceContract $webHistoricalPriceRepo
     * @return void
     */
    public function handle(WebProductContract $webProductRepo, WebHistoricalPriceContract $webHistoricalPriceRepo)
    {
        $this->webProductModel = new WebProduct;
        $this->productData = $this->__getData($this->data);
        $this->webHistoricalPriceRepo = $webHistoricalPriceRepo;
        if (array_has($this->productData, 'retailer_product_id')) {
            $existingWebProduct = $webProductRepo->findBy($this->retailer, 'retailer_product_id', array_get($this->data, 'retailer_product_id'))->first();
        } elseif (array_has($this->productData, 'slug')) {
            $existingWebProduct = $webProductRepo->findBy($this->retailer, 'slug', array_get($this->productData, 'slug'))->first();
        }
        if (!isset($existingWebProduct) || is_null($existingWebProduct)) {
            /*new product, set recent price to it*/
            array_set($this->productData, 'recent_price', array_get($this->data, 'price'));
            $existingWebProduct = $webProductRepo->store($this->productData);

            if (!is_null(array_get($this->data, 'price'))) {
                $this->savePrice($existingWebProduct, array_get($this->data, 'price'));
            }
        } else {
            /*existing product*/
            if (is_null($existingWebProduct->last_scraped_at) || array_get($this->data, 'last_scraped_at')->gt($existingWebProduct->last_scraped_at)) {
                if (!is_null(array_get($this->data, 'price'))) {
                    if (is_null($existingWebProduct->recent_price)) {
                        /*set recent price*/
                        array_set($this->productData, 'recent_price', array_get($this->data, 'price'));
                    } else {
                        /*check price change and set recent/previous price*/
                        $currentAmount = $existingWebProduct->recent_price;
                        $newAmount = round(floatval(array_get($this->data, 'price')), 2);
                        if (abs($currentAmount - $newAmount) > config('number.epsilon')) {
                            array_set($this->productData, 'previous_price', $existingWebProduct->recent_price);
                            array_set($this->productData, 'recent_price', array_get($this->data, 'price'));
                            array_set($this->productData, 'price_changed_at', array_get($this->data, 'last_scraped_at'));
                        }
                    }
                    $this->savePrice($existingWebProduct, array_get($this->data, 'price'));
                }
                /*update existing product*/
                $webProductRepo->update($existingWebProduct, $this->productData);
            }
        }

        $this->retailer->webProducts()->save($existingWebProduct);
        if (!is_null($this->webCategory)) {
            $existingWebProduct->webCategories()->syncWithoutDetaching([$this->webCategory->getKey()]);
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
