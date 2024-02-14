<?php

namespace App\Listeners;

use App\Events\DestroyGroupEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
 
class DestroyGroupListener implements ShouldQueue
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
    public function handle(DestroyGroupEvent $event): void
    {
        //
    }
}
