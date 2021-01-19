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

Broadcast::channel('chatrooms.{id}', function ($user, $chatroomId) {
    return Chatroom::find($chatroomId)->hasUser($user);
});

Broadcast::channel('user.{id}.readMessages', function ($user, $userId) {
    return $user->id == $userId;
});

Broadcast::channel('commented_assignment.{id}.group.{gid}', function ($user, $aId, $gId) {
    return Group::find($gId)->hasUser($user);
});

Broadcast::channel('commented_event.{id}.group.{gid}', function ($user, $eId, $gId) {
    return Group::find($gId)->hasUser($user);
});

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
