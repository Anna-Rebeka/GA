<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\MessageSent' => [
            'App\Listeners\SendChatMessageNotification'
        ],
        'App\Events\MessagesRead' => [
            'App\Listeners\MessagesReadNotification'
        ],
        'App\Events\EventCommented' => [
            'App\Listeners\EventCommentedNotification'
        ],
        'App\Events\AssignmentCommented' => [
            'App\Listeners\AssignmentCommentedNotification'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
