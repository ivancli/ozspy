<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 15/01/2018
 * Time: 11:24 PM
 */

namespace OzSpy\Contracts\Models\Auth;


use Illuminate\Support\Collection;
use OzSpy\Contracts\Models\BaseContract;
use OzSpy\Models\Auth\User;

abstract class UserContract extends BaseContract
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Get a collection of users by an attribute
     * @param $attributes
     * @param $value
     * @return Collection
     */
    abstract public function findBy($attributes, $value);

}