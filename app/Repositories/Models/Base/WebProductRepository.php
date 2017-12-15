<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/11/2017
 * Time: 6:16 PM
 */

namespace OzSpy\Repositories\Models\Base;


use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Models\Base\Retailer;
use OzSpy\Models\Base\WebProduct;

class WebProductRepository extends WebProductContract
{
    /**
     * @param Retailer $retailer
     * @param $element
     * @param $value
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function findBy(Retailer $retailer, $element, $value, $trashed = false)
    {
        $queryBuilder = $retailer->webProducts();
        if ($trashed === true) {
            $queryBuilder = $queryBuilder->withTrashed();
        }
        $queryBuilder->where($element, $value);
        return $queryBuilder->get();
    }

    /**
     * @param Retailer $retailer
     * @param $element
     * @param $value
     * @param bool $trashed
     * @return bool
     */
    public function exist(Retailer $retailer, $element, $value, $trashed = false)
    {
        $queryBuilder = $retailer->webProducts();
        if ($trashed === true) {
            $queryBuilder = $queryBuilder->withTrashed();
        }
        $queryBuilder->where($element, $value);
        return $queryBuilder->count() > 0;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function insertAll(array $data)
    {
        return $this->model->createAll($data);
    }
}