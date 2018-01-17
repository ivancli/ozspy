<?php

namespace OzSpy\Policies\Models;

use OzSpy\Models\Auth\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UsersPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the user.
     *
     * @param  \OzSpy\Models\Auth\User $user
     * @param  \OzSpy\Models\Auth\User $user
     * @return mixed
     */
    public function view(User $user, User $user)
    {
        //
    }

    /**
     * Determine whether the user can create users.
     *
     * @param  \OzSpy\Models\Auth\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the user.
     *
     * @param  \OzSpy\Models\Auth\User $user
     * @param  \OzSpy\Models\Auth\User $user
     * @return mixed
     */
    public function update(User $user, User $user)
    {
        //
    }

    /**
     * Determine whether the user can delete the user.
     *
     * @param  \OzSpy\Models\Auth\User $user
     * @param  \OzSpy\Models\Auth\User $user
     * @return mixed
     */
    public function delete(User $user, User $user)
    {
        //
    }
}
