<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2/10/2017
 * Time: 11:36 PM
 */

namespace App\Exceptions\SocialAuthExceptions;


class UnauthorisedException extends \Exception
{
    protected $message = 'Authorisation incomplete. Please authorise this application to access your profile.';

    protected $code = 401;
}