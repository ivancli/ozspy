<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 14/01/2018
 * Time: 1:11 AM
 */

namespace OzSpy\Services\Entities\User;

/**
 * Class StoreService
 * @package OzSpy\Services\Entities\User
 */
class StoreService extends UserServiceContract
{

    public function handle(array $data = [])
    {
        $this->userRepo->store($data);
    }
}