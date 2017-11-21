<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/11/2017
 * Time: 12:13 AM
 */

namespace OzSpy\Contracts\Models\Base;


use Illuminate\Database\Eloquent\Model;
use OzSpy\Models\Base\Brand;

abstract class BrandContract extends BaseContract
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }
}