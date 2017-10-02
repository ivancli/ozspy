<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 30/09/2017
 * Time: 12:15 AM
 */

namespace App\Contracts\Models;


use App\Models\User;
use Illuminate\Support\Collection;

interface UserContract
{
    /**
     * Get a collection of users by an attribute
     * @param $attributes
     * @param $value
     * @return Collection
     */
    public function findBy($attributes, $value);

    /**
     * @param array $data
     * @return User
     */
    public function create(array $data);

    /**
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function update(User $user, array $data);

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user);
}