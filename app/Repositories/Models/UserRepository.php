<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 30/09/2017
 * Time: 12:17 AM
 */

namespace App\Repositories\Models;


use App\Contracts\Models\UserContract;
use App\Models\User;
use Illuminate\Support\Collection;

class UserRepository implements UserContract
{
    protected $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }


    /**
     * Get a collection of users by an attribute
     * @param $attributes
     * @param $value
     * @return Collection
     */
    public function findBy($attributes, $value)
    {
        return $this->userModel->where($attributes, $value)->get();
    }

    /**
     * @param array $data
     * @return User
     */
    public function create(array $data)
    {
        $user = $this->userModel->create($data);
        return $user;
    }

    /**
     * @param User $user
     * @param array $data
     * @return bool
     */
    public function update(User $user, array $data)
    {
        return $user->update($data);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->delete();
    }
}