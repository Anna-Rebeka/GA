<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chatroom;
use App\Models\Group;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('chatrooms.{id}', function ($user, $chatroom_id) {
    return Chatroom::find($chatroom_id)->hasUser($user);
});

Broadcast::channel('user.{id}.readMessages', function ($user, $user_id) {
    return $user->id == $user_id;
});

Broadcast::channel('commented_assignment.{id}.group.{gid}', function ($user, $assignment_id, $group_id) {
    return Group::find($group_id)->hasUser($user);
});

Broadcast::channel('commented_event.{id}.group.{gid}', function ($user, $event_id, $group_id) {
    return Group::find($group_id)->hasUser($user);
});

Broadcast::channel('groups.{id}', function ($user, $group_id) {
    return Group::find($group_id)->hasUser($user);
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
