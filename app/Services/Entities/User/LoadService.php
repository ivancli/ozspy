<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 14/01/2018
 * Time: 1:36 AM
 */

namespace OzSpy\Services\Entities\User;


class LoadService extends UserServiceContract
{

    public function handle()
    {
        if (!is_null($this->authUser)) {

        }

        $users = $this->userRepo->all();
        return $users;
    }

    protected function loadMembers()
    {

    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\OzSpy\Models\Model[]
     */
    protected function loadAllUsers()
    {
        return $this->userRepo->all();
    }
}