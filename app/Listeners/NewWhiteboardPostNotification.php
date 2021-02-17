<?php

namespace App\Listeners;

use App\Events\NewWhiteboardPost;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NewWhiteboardPostNotification
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
     * @param  NewWhiteboardPost  $event
     * @return void
     */
    public function handle(NewWhiteboardPost $event)
    {
        //
    }
}
