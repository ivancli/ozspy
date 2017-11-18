<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 30/09/2017
 * Time: 12:20 AM
 */

namespace OzSpy\Providers;


use OzSpy\Contracts\Models\Auth\UserContract;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Common\CountryContract;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Repositories\Models\Auth\UserRepository;
use Illuminate\Support\ServiceProvider;
use OzSpy\Repositories\Models\Base\RetailerRepository;
use OzSpy\Repositories\Models\Common\CountryRepository;
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

        $this->app->bind(CountryContract::class, CountryRepository::class);

        $this->app->bind(RetailerContract::class, RetailerRepository::class);
    }
}