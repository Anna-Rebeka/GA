<?php

namespace App\Events;

use App\Models\User;
use App\Models\EventComment;
use App\Models\Event;
use App\Models\Group;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventCommentDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $event_comment;
    public $event;
    public $group;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, EventComment $event_comment, Event $event, Group $group)
    {
        $this->user = $user;
        $this->event_comment = $event_comment;
        $this->event = $event;
        $this->group = $group;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('commented_event.' . $this->event->id . '.group.' . $this->group->id) . '.deleted';
    }
}
