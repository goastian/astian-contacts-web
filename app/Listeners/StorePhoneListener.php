<?php

namespace App\Listeners;

use App\Events\StorePhoneEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StorePhoneListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(StorePhoneEvent $event): void
    {
        //
    }
}
