<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/11/2017
 * Time: 12:13 AM
 */

namespace OzSpy\Contracts\Models\Base;


use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebProduct;

abstract class WebProductContract extends BaseContract
{
    public function __construct(WebProduct $model)
    {
        parent::__construct($model);
    }

    /**
     * @param Retailer $retailer
     * @param $element
     * @param $value
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    abstract public function findBy(Retailer $retailer, $element, $value, $trashed = false);

    /**
     * @param Retailer $retailer
     * @param $element
     * @param $value
     * @param bool $trashed
     * @return bool
     */
    abstract public function exist(Retailer $retailer, $element, $value, $trashed = false);
}