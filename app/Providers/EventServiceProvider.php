<?php

namespace OzSpy\Providers;

use OzSpy\Models\Auth\User;
use OzSpy\Observers\UserObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'OzSpy\Events\Event' => [
            'OzSpy\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerObservers();

        parent::boot();

        //
    }

    protected function registerObservers()
    {
        User::observe(UserObserver::class);
    }
}
