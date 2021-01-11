<?php

namespace App\Http\Controllers;

use App\Models\Chatroom;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatroomsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $chatrooms = $user->chatrooms;
        $messages = [];

        foreach ($chatrooms as $chatroom){
            $chatroom['users'] = $chatroom->users()->where('users.id', '!=', $user->id)->get();
            $chatroom->latestMessage;
        }

        return view('chatrooms.index', [
            'user' => auth()->user(),
            'chatrooms' => $chatrooms,
        ]);
    }

    public function getMessages(Chatroom $chatroom){
        return Message::where('chatroom_id', $chatroom->id)
            ->with('sender')
            ->orderBy('created_at', 'DESC')
            ->limit(30)
            ->get();
    }

    public function loadOlderMessages(Chatroom $chatroom, $howManyDisplayed){
        return Message::where('chatroom_id', $chatroom->id)
            ->with('sender')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->offset($howManyDisplayed)
            ->get();
    }


    public function getUsers(Chatroom $chatroom, User $user){
        return $chatroom->users()->where('users.id', '!=', $user->id)->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chatroom  $chatroom
     * @return \Illuminate\Http\Response
     */
    public function show(Chatroom $chatroom)
    {   
        $chatroom->users;
        return view('chatrooms.show', [
            'user' => auth()->user(),
            'chatroom' => $chatroom,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chatroom  $chatroom
     * @return \Illuminate\Http\Response
     */
    public function edit(Chatroom $chatroom)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chatroom  $chatroom
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chatroom $chatroom)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chatroom  $chatroom
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chatroom $chatroom)
    {
        //
    }

    public function findOrCreateChatroom(User $user){
        $authed = auth()->user();
        $chatrooms = $authed->chatrooms()->with('users')->get();
        foreach($chatrooms as $chatroom){
            if( count($chatroom->users) == 2 
                && $chatroom->users->contains($authed) 
                && $chatroom->users->contains($user)){
                    return redirect()->route('showchat', ['chatroom' => $chatroom->id]);
                }
        }
        $new_chatroom = Chatroom::create([
        ]);
        $new_chatroom->users()->attach($authed->id);
        $new_chatroom->users()->attach($user->id);
        return redirect()->route('showchat', ['chatroom' => $new_chatroom->id]);
    }

    public function markAsReadMessages(Chatroom $chatroom){
        $user = auth()->user();
        $affected = DB::table('messages')->where('chatroom_id', $chatroom->id)->where('sender_id', '!=', $user->id)->update(['read' => 1]);
        return $affected;
    }

    public function getLatestMessage(Chatroom $chatroom){
        return $chatroom->messages()->with('sender')->latest()->first();
    }
}
