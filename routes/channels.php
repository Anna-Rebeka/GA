<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Chatroom;

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


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
