<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 19/11/2017
 * Time: 1:10 AM
 */

namespace OzSpy\Repositories\Models\Base;


use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Models\Base\Retailer;

class RetailerRepository implements RetailerContract
{
    protected $retailer;

    public function __construct(Retailer $retailer)
    {
        $this->retailer = $retailer;
    }

    /**
     * get all countries
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|Retailer[]
     */
    public function all($trashed = false)
    {
        if ($trashed === true) {
            return $this->retailer->withTrashed()->get();
        } else {
            return $this->retailer->all();
        }
    }

    /**
     * get retailer by id
     * @param $id
     * @param bool $throw
     * @return Retailer|null
     */
    public function get($id, $throw = true)
    {
        if ($throw === true) {
            return $this->retailer->withTrashed()->findOrFail($id);
        } else {
            return $this->retailer->withTrashed()->find($id);
        }
    }

    /**
     * create a new retailer
     * @param array $data
     * @return Retailer
     */
    public function store(array $data)
    {
        $data = $this->__getData($data);
        return $this->retailer->create($data);
    }

    /**
     * update an existing retailer
     * @param Retailer $retailer
     * @param array $data
     * @return bool
     */
    public function update(Retailer $retailer, array $data)
    {
        $data = $this->__getData($data);
        return $retailer->update($data);
    }

    /**
     * delete a retailer
     * @param Retailer $retailer
     * @param bool $force
     * @return bool
     */
    public function delete(Retailer $retailer, $force = false)
    {
        if ($force === true) {
            return $retailer->forceDelete();
        } else {
            return $retailer->delete();
        }
    }

    /**
     * restore a retailer
     * @param Retailer $retailer
     * @return bool
     */
    public function restore(Retailer $retailer)
    {
        return $retailer->restore();
    }

    /**
     * filter parameters
     * @param array $data
     * @return array
     */
    private function __getData(array $data)
    {
        return array_only($data, $this->retailer->getFillable());
    }
}