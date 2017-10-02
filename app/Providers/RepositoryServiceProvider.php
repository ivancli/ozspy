<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 30/09/2017
 * Time: 12:20 AM
 */

namespace App\Providers;


use App\Contracts\Models\UserContract;
use App\Repositories\Models\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserContract::class, UserRepository::class);
    }
}