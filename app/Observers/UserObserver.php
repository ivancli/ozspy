<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 9/10/2017
 * Time: 10:15 PM
 */

namespace App\Observers;


use App\Models\Role;
use App\Models\User;

class UserObserver
{
    protected $roleModel;

    public function __construct(Role $roleModel)
    {
        $this->roleModel = $roleModel;
    }

    public function retrieved()
    {

    }

    public function creating()
    {

    }

    public function created(User $user)
    {
        $clientRole = $this->roleModel->getClientRole();
        if (!is_null($clientRole)) {
            $user->attachRole($clientRole);
        }
    }

    public function updating()
    {

    }

    public function updated()
    {

    }

    public function saving()
    {

    }

    public function saved()
    {

    }

    public function deleting()
    {

    }

    public function deleted()
    {

    }

    public function restoring()
    {

    }

    public function restored()
    {

    }
}