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
            $existingWebProduct = $webProductRepo->store($this->productData);
        } else {
            $webProductRepo->update($existingWebProduct, $this->productData);
        }
        if (!is_null(array_get($this->data, 'price'))) {
            $this->savePrice($existingWebProduct, array_get($this->data, 'price'));
        }
        $this->retailer->webProducts()->save($existingWebProduct);
        if (!is_null($this->webCategory)) {
            $existingWebProduct->webCategories()->syncWithoutDetaching([$this->webCategory->getKey()]);
        }
    }

    private function savePrice(WebProduct $webProduct, $price)
    {
        if (is_null($webProduct->recent_price)) {
            $webProduct->recent_price = $price;
            $webProduct->save();
        } else {
            $currentAmount = $webProduct->recent_price;
            $newAmount = round(floatval(array_get($price, 'amount')), 2);
            if (abs($currentAmount - $newAmount) > config('number.epsilon')) {
                $webProduct->previous_price = $webProduct->recent_price;
                $webProduct->recent_price = $price;
                $webProduct->price_changed_at = Carbon::now();
                $webProduct->save();
            }
        }

        $this->webHistoricalPriceRepo->storeIfNull($webProduct, [
            'amount' => $price
        ]);
    }

    private function __getData(array $data)
    {
        return array_only($data, $this->webProductModel->getFillable());
    }
}
