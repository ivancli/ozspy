<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/01/2018
 * Time: 5:44 PM
 */

namespace OzSpy\Exceptions\Models;


class DuplicateProductException extends \Exception
{
    protected $message = 'Product already exist';

    protected $code = 409;
}