<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 22/11/2017
 * Time: 10:26 PM
 */

namespace OzSpy\Exceptions;


class ScraperNotFoundException extends \Exception
{
    protected $message = 'Scraper cannot be found.';

    protected $code = 404;
}