<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 18/01/2018
 * Time: 10:49 PM
 */

namespace OzSpy\Services;

/**
 * Class ServiceContract
 * @package OzSpy\Services
 */
abstract class ServiceContract
{
    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable|\OzSpy\Models\Auth\User
     */
    protected $authUser;

    public function __construct()
    {
        if (auth()->check()) {
            $this->authUser = auth()->user();

            //logging
            $this->__log();
        }
    }

    /**
     * log user activities
     */
    private function __log()
    {
        $class = get_called_class();
        $paths = explode('\\', $class);
        $servicePaths = array_where($paths, function ($path, $key) {
            return !in_array($path, [
                config('app.name'),
                'Services'
            ]);
        });
        /*TODO record log in file or db*/
    }
}