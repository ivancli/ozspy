<?php

namespace OzSpy\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        \DB::listen(function ($query) {
            if ($query->time > 10) {
                $view_log = new Logger('Query Log');
                $view_log->pushHandler(new StreamHandler(storage_path('logs/query.log'), Logger::INFO));
                $view_log->addInfo("Slow query run on DB", [
                    'date' => Carbon::now(),
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time
                ]);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
