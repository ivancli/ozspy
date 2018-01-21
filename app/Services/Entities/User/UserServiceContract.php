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
abstract class UserServiceContract extends ServiceContract
{
    /**
     * @var UserContract
     */
    protected $userRepo;

    /**
     * UserServiceContract constructor.
     * @param UserContract $userContract
     */
    public function __construct(UserContract $userContract)
    {
        parent::__construct();

        $this->userRepo = $userContract;
    }
}