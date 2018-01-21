<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 18/01/2018
 * Time: 10:22 PM
 */

namespace OzSpy\Contracts\Models\Auth;


use OzSpy\Contracts\Models\BaseContract;
use OzSpy\Models\Auth\Group;

abstract class GroupContract extends BaseContract
{
    public function __construct(Group $model)
    {
        parent::__construct($model);
    }
}