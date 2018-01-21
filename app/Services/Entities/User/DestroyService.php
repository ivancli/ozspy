<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 14/01/2018
 * Time: 1:11 AM
 */

namespace OzSpy\Services\Entities\User;

use OzSpy\Models\Auth\User;

/**
 * Class DestroyService
 * @package OzSpy\Services\Entities\User
 */
class DestroyService extends UserServiceContract
{

    public function handle(User $user)
    {
        $result = $this->userRepo->delete($user);
    }
}