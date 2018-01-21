<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 18/01/2018
 * Time: 10:21 PM
 */

namespace OzSpy\Services\Entities\Group;


use OzSpy\Contracts\Models\Auth\GroupContract;
use OzSpy\Services\ServiceContract;

/**
 * Class GroupServiceContract
 * @package OzSpy\Services\Entities\Group
 */
abstract class GroupServiceContract extends ServiceContract
{
    /**
     * @var GroupContract
     */
    protected $groupRepo;

    /**
     * UserServiceContract constructor.
     * @param GroupContract $groupContract
     */
    public function __construct(GroupContract $groupContract)
    {
        parent::__construct();

        $this->groupRepo = $groupContract;
    }
}