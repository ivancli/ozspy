<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 19/11/2017
 * Time: 1:08 AM
 */

namespace OzSpy\Contracts\Models\Base;


use OzSpy\Contracts\Models\BaseContract;
use OzSpy\Models\Base\Retailer;

abstract class RetailerContract extends BaseContract
{
    public function __construct(Retailer $model)
    {
        parent::__construct($model);
    }
}