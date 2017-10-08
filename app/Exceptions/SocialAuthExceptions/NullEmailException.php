<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 2/10/2017
 * Time: 3:25 PM
 */

namespace App\Exceptions\SocialAuthExceptions;


class NullEmailException extends \Exception
{
    protected $message = 'Unable to retrieve email address from social networking profile.';

    protected $code = 401;
}