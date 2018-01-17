<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 14/01/2018
 * Time: 1:36 AM
 */

namespace OzSpy\Services\Entities\User;


use OzSpy\Contracts\Models\Auth\UserContract;
use OzSpy\Services\ServiceContract;

/**
 * Class UserServiceContract
 * @package OzSpy\Services\Entities\User
 */
abstract class UserServiceContract implements ServiceContract
{
    /**
     * @var UserContract
     */
    protected $userRepo;

    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable|\OzSpy\Models\Auth\User
     */
    protected $authUser;

    /**
     * UserServiceContract constructor.
     * @param UserContract $userContract
     */
    public function __construct(UserContract $userContract)
    {
        if (auth()->check()) {
            $this->authUser = auth()->user();
        }

        $this->userRepo = $userContract;
    }
}