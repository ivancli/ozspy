<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 19/11/2017
 * Time: 2:49 PM
 */

namespace OzSpy\Contracts\Models\Base;


use OzSpy\Models\Base\WebCategory;
use OzSpy\Models\Base\Retailer;

abstract class WebCategoryContract extends BaseContract
{
    public function __construct(WebCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * @param Retailer $retailer
     * @param $name
     * @param WebCategory|null $parentCategory
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    abstract public function findByName(Retailer $retailer, $name, WebCategory $parentCategory = null, $trashed = false);

    /**
     * @param Retailer $retailer
     * @param $name
     * @param WebCategory|null $parentCategory
     * @param bool $trashed
     * @return bool
     */
    abstract public function exist(Retailer $retailer, $name, WebCategory $parentCategory = null, $trashed = false);
}