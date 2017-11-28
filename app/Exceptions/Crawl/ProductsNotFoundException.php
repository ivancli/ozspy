<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 25/11/2017
 * Time: 1:49 PM
 */

namespace OzSpy\Exceptions\Crawl;


class ProductsNotFoundException extends \Exception
{
    protected $message = 'Products cannot be found.';

    protected $code = 404;
}