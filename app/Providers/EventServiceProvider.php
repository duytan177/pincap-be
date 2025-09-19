<?php

namespace App\Providers;

use App\Events\MediaCreatedEvent;
use App\Listeners\MediaCreatedListener;
use App\Events\AlbumInvitationEvent;
use App\Listeners\AlbumInvitationListener;
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
        AlbumInvitationEvent::class => [
          AlbumInvitationListener::class,
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
