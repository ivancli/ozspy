<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/01/2018
 * Time: 5:41 PM
 */

namespace OzSpy\Exceptions\Models;


class DuplicateCategoryException extends \Exception
{
    protected $message = 'Category already exist';

    protected $code = 409;
}