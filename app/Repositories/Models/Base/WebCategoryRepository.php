<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 19/11/2017
 * Time: 2:51 PM
 */

namespace OzSpy\Repositories\Models\Base;


use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Models\Base\WebCategory;
use OzSpy\Models\Base\Retailer;

class WebCategoryRepository extends WebCategoryContract
{
    /**
     * @param Retailer $retailer
     * @param $name
     * @param WebCategory|null $parentCategory
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    final public function findByName(Retailer $retailer, $name, WebCategory $parentCategory = null, $trashed = false)
    {
        if (!is_null($parentCategory)) {
            $queryBuilder = $parentCategory->childCategories();
            if ($trashed === true) {
                $queryBuilder = $queryBuilder->withTrashed();
            }
            return $queryBuilder->where('name', $name)->first();
        }
        $queryBuilder = $retailer->webCategories();
        if ($trashed === true) {
            $queryBuilder->withTrashed();
        }
        return $queryBuilder->where('name', $name)->first();
    }

    /**
     * @param Retailer $retailer
     * @param $name
     * @param WebCategory|null $parentCategory
     * @param bool $trashed
     * @return bool
     */
    final public function exist(Retailer $retailer, $name, WebCategory $parentCategory = null, $trashed = false)
    {
        if (!is_null($parentCategory)) {
            $queryBuilder = $parentCategory->childCategories();
            if ($trashed === true) {
                $queryBuilder = $queryBuilder->withTrashed();
            }
            return $queryBuilder->where('name', $name)->count() > 0;
        }

        $queryBuilder = $retailer->webCategories();
        if ($trashed === true) {
            $queryBuilder->withTrashed();
        }
        return $queryBuilder->where('name', $name)->count() > 0;
    }
}