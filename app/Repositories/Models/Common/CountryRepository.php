<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 18/11/2017
 * Time: 9:39 PM
 */

namespace OzSpy\Repositories\Models\Common;


use OzSpy\Contracts\Models\Common\CountryContract;
use OzSpy\Models\Common\Country;

class CountryRepository implements CountryContract
{
    protected $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    /**
     * get all countries
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|Country[]
     */
    public function all($trashed = false)
    {
        if ($trashed === true) {
            return $this->country->withTrashed()->get();
        } else {
            return $this->country->all();
        }
    }

    /**
     * get country by id
     * @param $id
     * @param bool $throw
     * @return Country|null
     */
    public function get($id, $throw = true)
    {
        if ($throw === true) {
            return $this->country->withTrashed()->findOrFail($id);
        } else {
            return $this->country->withTrashed()->find($id);
        }
    }

    /**
     * create a new country
     * @param array $data
     * @return Country
     */
    public function store(array $data)
    {
        $data = $this->__getData($data);
        return $this->country->create($data);
    }

    /**
     * update an existing country
     * @param Country $country
     * @param array $data
     * @return bool
     */
    public function update(Country $country, array $data)
    {
        $data = $this->__getData($data);
        return $country->update($data);
    }

    /**
     * delete a country
     * @param Country $country
     * @param bool $force
     * @return bool
     */
    public function delete(Country $country, $force = false)
    {
        if ($force === true) {
            return $country->forceDelete();
        } else {
            return $country->delete();
        }
    }

    /**
     * restore a country
     * @param Country $country
     * @return bool
     */
    public function restore(Country $country)
    {
        return $country->restore();
    }

    /**
     * filter parameters
     * @param array $data
     * @return array
     */
    private function __getData(array $data)
    {
        return array_only($data, $this->country->getFillable());
    }
}