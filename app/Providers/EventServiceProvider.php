<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        \App\Events\StoreContactEvent::class => [
            \App\Listeners\StoreContactListener::class,
        ],

        \App\Events\UpdateContactEvent::class => [
            \App\Listeners\UpdateContactListener::class,
        ],

        \App\Events\DestroyContactEvent::class => [
            \App\Listeners\DestroyContactListener::class,
        ],

        \App\Events\StoreGroupEvent::class => [
            \App\Listeners\StoreGroupListener::class,
        ],

        \App\Events\UpdateGroupEvent::class => [
            \App\Listeners\UpdateGroupListener::class,
        ],

        \App\Events\DestroyGroupEvent::class => [
            \App\Listeners\DestroyGroupListener::class,
        ],

        \App\Events\StoreAppEvent::class => [
            \App\Listeners\StoreAppListener::class,
        ],

        \App\Events\UpdateAppEvent::class => [
            \App\Listeners\UpdateAppListener::class,
        ],

        \App\Events\DestroyAppEvent::class => [
            \App\Listeners\DestroyAppListener::class,
        ],

        \App\Events\StoreEmailEvent::class => [
            \App\Listeners\StoreEmailListener::class,
        ],

        \App\Events\UpdateEmailEvent::class => [
            \App\Listeners\UpdateEmailListener::class,
        ],

        \App\Events\DestroyEmailEvent::class => [
            \App\Listeners\DestroyAppListener::class,
        ],

        \App\Events\StorePhoneEvent::class => [
            \App\Listeners\StorePhoneListener::class,
        ],

        \App\Events\UpdatePhoneEvent::class => [
            \App\Listeners\UpdatePhoneListener::class,
        ],

        \App\Events\DestroyPhoneEvent::class => [
            \App\Listeners\DestroyPhoneListener::class,
        ],

    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
