<?php

namespace App\Listeners;

use App\Events\MessagesRead;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class MessagesReadNotification
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
     * @param  MessagesRead  $event
     * @return void
     */
    public function handle(MessagesRead $event)
    {
        //
    }
}
