<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 21/01/2018
 * Time: 2:04 PM
 */

namespace OzSpy\Exceptions\Models;


class RoleRelationshipNotFound extends \Exception
{
    protected $message = 'The Roleable entity has no relationship with Role.';

    protected $code = 500;
}