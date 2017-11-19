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

class WebCategoryRepository implements WebCategoryContract
{
    protected $category;

    public function __construct(WebCategory $category)
    {
        $this->category = $category;
    }

    /**
     * get all countries
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Collection|WebCategory[]
     */
    public function all($trashed = false)
    {
        if ($trashed === true) {
            return $this->category->withTrashed()->get();
        } else {
            return $this->category->all();
        }
    }

    /**
     * get country by id
     * @param $id
     * @param bool $throw
     * @return WebCategory|null
     */
    public function get($id, $throw = true)
    {
        if ($throw === true) {
            return $this->category->withTrashed()->findOrFail($id);
        } else {
            return $this->category->withTrashed()->find($id);
        }
    }

    /**
     * @param Retailer $retailer
     * @param $name
     * @param WebCategory|null $parentCategory
     * @param bool $trashed
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function findByName(Retailer $retailer, $name, WebCategory $parentCategory = null, $trashed = false)
    {
        if (!is_null($parentCategory)) {
            $queryBuilder = $parentCategory->childCategories();
            if ($trashed === true) {
                $queryBuilder = $queryBuilder->withTrashed();
            }
            return $queryBuilder->where('name', $name)->first();
        }
        $queryBuilder = $retailer->categories();
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
    public function exist(Retailer $retailer, $name, WebCategory $parentCategory = null, $trashed = false)
    {
        if (!is_null($parentCategory)) {
            $queryBuilder = $parentCategory->childCategories();
            if ($trashed === true) {
                $queryBuilder = $queryBuilder->withTrashed();
            }
            return $queryBuilder->where('name', $name)->count() > 0;
        }

        $queryBuilder = $retailer->categories();
        if ($trashed === true) {
            $queryBuilder->withTrashed();
        }
        return $queryBuilder->where('name', $name)->count() > 0;
    }

    /**
     * create a new country
     * @param array $data
     * @return WebCategory
     */
    public function store(array $data)
    {
        $data = $this->__getData($data);
        return $this->category->create($data);
    }

    /**
     * update an existing country
     * @param WebCategory $category
     * @param array $data
     * @return bool
     */
    public function update(WebCategory $category, array $data)
    {
        $data = $this->__getData($data);
        return $category->update($data);
    }

    /**
     * delete a country
     * @param WebCategory $category
     * @param bool $force
     * @return bool
     */
    public function delete(WebCategory $category, $force = false)
    {
        if ($force === true) {
            return $category->forceDelete();
        } else {
            return $category->delete();
        }
    }

    /**
     * restore a country
     * @param WebCategory $category
     * @return bool
     */
    public function restore(WebCategory $category)
    {
        return $category->restore();
    }

    /**
     * filter parameters
     * @param array $data
     * @return array
     */
    private function __getData(array $data)
    {
        return array_only($data, $this->category->getFillable());
    }
}