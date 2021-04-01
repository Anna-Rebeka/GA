<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\User;
use App\Models\Assignment;
use App\Models\Group;
use App\Models\AssignmentsComments;


class AssignmentCommentDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public $assignment_comment;
    public $assignment;
    public $group;




    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, AssignmentsComments $assignment_comment, Assignment $assignment, Group $group)
    {
        $this->user = $user;
        $this->assignment_comment = $assignment_comment;
        $this->assignment = $assignment;
        $this->group = $group;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('commented_assignment.' . $this->assignment->id . '.group.' . $this->group->id . '.deleted');
    }
}
