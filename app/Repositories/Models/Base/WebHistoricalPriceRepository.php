<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 24/11/2017
 * Time: 9:25 PM
 */

namespace OzSpy\Repositories\Models\Base;


use OzSpy\Contracts\Models\Base\WebHistoricalPriceContract;
use OzSpy\Models\Base\WebHistoricalPrice;
use OzSpy\Models\Base\WebProduct;

class WebHistoricalPriceRepository extends WebHistoricalPriceContract
{

    /**
     * @param WebProduct $webProduct
     * @param array $data
     * @return WebHistoricalPrice
     */
    public function storeIfNull(WebProduct $webProduct, array $data)
    {
        $diff = false;
        if (is_null($webProduct->recentWebHistoricalPrice)) {
            $diff = true;
        } else {
            $currentAmount = $webProduct->recentWebHistoricalPrice->amount;
            $newAmount = round(floatval(array_get($data, 'amount')), 2);
            if (abs($currentAmount - $newAmount) > config('number.epsilon')) {
                $diff = true;
            }
        }
        if ($diff === true) {
            $webPriceHistoricalPrice = $this->store($data);
            $webProduct->webHistoricalPrices()->save($webPriceHistoricalPrice);
            return $webPriceHistoricalPrice;
        }
        return $webProduct->recentWebHistoricalPrice;
    }
}