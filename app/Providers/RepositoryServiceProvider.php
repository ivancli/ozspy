<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 30/09/2017
 * Time: 12:20 AM
 */

namespace OzSpy\Providers;


use OzSpy\Contracts\Models\Auth\UserContract;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Repositories\Models\Auth\UserRepository;
use Illuminate\Support\ServiceProvider;
use OzSpy\Repositories\Models\Crawl\ProxyRepository;

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

        $this->app->bind(ProxyContract::class, ProxyRepository::class);
    }
}