<?php

namespace App\Events;

use App\Models\Message;
use App\Models\Chatroom;
use App\Models\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatroom;
    public $message;
    public $sender;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Message $message, Chatroom $chatroom, User $sender)
    {
        $this->chatroom = $chatroom;
        $this->message = $message;
        $this->sender = $sender;
    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chatrooms.'.$this->chatroom->id);
    }
}
