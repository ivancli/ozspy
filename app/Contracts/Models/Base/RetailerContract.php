<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 19/11/2017
 * Time: 1:08 AM
 */

namespace OzSpy\Contracts\Models\Base;


use OzSpy\Models\Base\Retailer;

interface RetailerContract
{

    /**
     * get all countries
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|Retailer[]
     */
    public function all($trashed = false);

    /**
     * get country by id
     * @param $id
     * @param bool $throw
     * @return Retailer|null
     */
    public function get($id, $throw = true);

    /**
     * create a new country
     * @param array $data
     * @return Retailer
     */
    public function store(array $data);

    /**
     * update an existing country
     * @param Retailer $retailer
     * @param array $data
     * @return bool
     */
    public function update(Retailer $retailer, array $data);

    /**
     * delete a country
     * @param Retailer $retailer
     * @param bool $force
     * @return bool
     */
    public function delete(Retailer $retailer, $force = false);

    /**
     * restore a country
     * @param Retailer $retailer
     * @return bool
     */
    public function restore(Retailer $retailer);
}