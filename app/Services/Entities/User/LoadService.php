<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 14/01/2018
 * Time: 1:36 AM
 */

namespace OzSpy\Services\Entities\User;

/**
 * Class LoadService
 * @package OzSpy\Services\Entities\User
 */
class LoadService extends UserServiceContract
{
    /**
     * @param array $data
     * @return array
     */
    public function handle(array $data = [])
    {
        if (!is_null($this->authUser)) {

        }

        //set pagination data
//        $this->setAttrs($data);
//
//        $users = $this->userRepo->all();
//
//        $this->data = $users;
//        $result = $this->composer();

//        return $result;
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