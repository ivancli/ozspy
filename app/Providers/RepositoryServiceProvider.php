<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 30/09/2017
 * Time: 12:20 AM
 */

namespace OzSpy\Providers;


use OzSpy\Contracts\Models\Auth\UserContract;
use OzSpy\Contracts\Models\Base\BrandContract;
use OzSpy\Contracts\Models\Base\WebBrandContract;
use OzSpy\Contracts\Models\Base\WebCategoryContract;
use OzSpy\Contracts\Models\Base\RetailerContract;
use OzSpy\Contracts\Models\Base\WebProductContract;
use OzSpy\Contracts\Models\Common\CountryContract;
use OzSpy\Contracts\Models\Crawl\ProxyContract;
use OzSpy\Repositories\Models\Auth\UserRepository;
use Illuminate\Support\ServiceProvider;
use OzSpy\Repositories\Models\Base\BrandRepository;
use OzSpy\Repositories\Models\Base\WebBrandRepository;
use OzSpy\Repositories\Models\Base\WebCategoryRepository;
use OzSpy\Repositories\Models\Base\RetailerRepository;
use OzSpy\Repositories\Models\Base\WebProductRepository;
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

        $this->app->bind(WebCategoryContract::class, WebCategoryRepository::class);

        $this->app->bind(WebProductContract::class, WebProductRepository::class);

        $this->app->bind(WebBrandContract::class, WebBrandRepository::class);

        $this->app->bind(BrandContract::class, BrandRepository::class);
    }
}