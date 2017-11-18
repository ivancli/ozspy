<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 18/11/2017
 * Time: 9:33 PM
 */

namespace OzSpy\Contracts\Models\Common;


use OzSpy\Models\Common\Country;

interface CountryContract
{

    /**
     * get all countries
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|Country[]
     */
    public function all($trashed = false);

    /**
     * get country by id
     * @param $id
     * @param bool $throw
     * @return Country|null
     */
    public function get($id, $throw = true);

    /**
     * create a new country
     * @param array $data
     * @return Country
     */
    public function store(array $data);

    /**
     * update an existing country
     * @param Country $country
     * @param array $data
     * @return bool
     */
    public function update(Country $country, array $data);

    /**
     * delete a country
     * @param Country $country
     * @param bool $force
     * @return bool
     */
    public function delete(Country $country, $force = false);

    /**
     * restore a country
     * @param Country $country
     * @return bool
     */
    public function restore(Country $country);
}