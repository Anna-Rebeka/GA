<?php

namespace App\Listeners;

use App\Events\EventCommented;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EventCommentedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  EventCommented  $event
     * @return void
     */
    public function handle(EventCommented $event)
    {
        //
    }
}
