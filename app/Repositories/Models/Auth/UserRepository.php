<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 30/09/2017
 * Time: 12:17 AM
 */

namespace OzSpy\Repositories\Models\Auth;


use Illuminate\Database\Eloquent\Builder;
use OzSpy\Contracts\Models\Auth\UserContract;
use Illuminate\Support\Collection;

class UserRepository extends UserContract
{

    /**
     * Get a collection of users by an attribute
     * @param $attributes
     * @param $value
     * @return Collection
     */
    public function findBy($attributes, $value)
    {
        return $this->builder()->where($attributes, $value)->get();
    }
}