<?php

namespace App\Listeners;

use App\Events\AssignmentCommented;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AssignmentCommentedNotification
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
     * @param  AssignmentCommented  $event
     * @return void
     */
    public function handle(AssignmentCommented $event)
    {
        //
    }
}
