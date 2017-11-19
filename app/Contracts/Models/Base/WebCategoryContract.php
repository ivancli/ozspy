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

interface WebCategoryContract
{
    /**
     * get all countries
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|WebCategory[]
     */
    public function all($trashed = false);

    /**
     * get country by id
     * @param $id
     * @param bool $throw
     * @return WebCategory|null
     */
    public function get($id, $throw = true);

    /**
     * @param Retailer $retailer
     * @param $name
     * @param WebCategory|null $parentCategory
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function findByName(Retailer $retailer, $name, WebCategory $parentCategory = null, $trashed = false);

    /**
     * @param Retailer $retailer
     * @param $name
     * @param WebCategory|null $parentCategory
     * @param bool $trashed
     * @return bool
     */
    public function exist(Retailer $retailer, $name, WebCategory $parentCategory = null, $trashed = false);

    /**
     * create a new country
     * @param array $data
     * @return WebCategory
     */
    public function store(array $data);

    /**
     * update an existing country
     * @param WebCategory $category
     * @param array $data
     * @return bool
     */
    public function update(WebCategory $category, array $data);

    /**
     * delete a country
     * @param WebCategory $category
     * @param bool $force
     * @return bool
     */
    public function delete(WebCategory $category, $force = false);

    /**
     * restore a country
     * @param WebCategory $category
     * @return bool
     */
    public function restore(WebCategory $category);
}