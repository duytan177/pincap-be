<?php

namespace App\Providers;

use App\Events\MediaCreatedEvent;
use App\Listeners\MediaCreatedListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     */
    protected $listen = [
        MediaCreatedEvent::class => [
          MediaCreatedListener::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
        parent::boot();
    }
}
