<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 24/11/2017
 * Time: 9:23 PM
 */

namespace OzSpy\Contracts\Models\Base;


use Illuminate\Database\Eloquent\Model;
use OzSpy\Models\Base\WebHistoricalPrice;
use OzSpy\Models\Base\WebProduct;

abstract class WebHistoricalPriceContract extends BaseContract
{
    public function __construct(WebHistoricalPrice $model)
    {
        parent::__construct($model);
    }

    /**
     * @param WebProduct $webProduct
     * @param array $data
     * @return WebHistoricalPrice
     */
    abstract public function storeIfNull(WebProduct $webProduct, array $data);
}