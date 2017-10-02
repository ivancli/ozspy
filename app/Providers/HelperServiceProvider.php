<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 1/10/2017
 * Time: 9:43 PM
 */

namespace App\Providers;


use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        foreach (glob(app_path() . '/Helpers/*.php') as $filename) {
            require_once($filename);
        }
    }
}