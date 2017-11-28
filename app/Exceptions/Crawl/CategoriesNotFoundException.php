<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 25/11/2017
 * Time: 1:47 PM
 */

namespace OzSpy\Exceptions\Crawl;


class CategoriesNotFoundException extends \Exception
{
    protected $message = 'Categories cannot be found.';

    protected $code = 404;
}