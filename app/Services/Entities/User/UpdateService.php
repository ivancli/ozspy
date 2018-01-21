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
 * Class UpdateService
 * @package OzSpy\Services\Entities\User
 */
class UpdateService extends UserServiceContract
{

    public function handle(User $user, array $data = [])
    {
        $this->userRepo->update($user, $data);
    }
}