<?php

namespace App\Http\Controllers;

use App\Models\Chatroom;
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
        $messages = [];


        //TODO make a table user <---> chatroom 
        /*
        foreach ($user->chatrooms() as $chatroom){
            $messages[$chatroom->id] = $chatroom->latestMessage;
        }
        */

        foreach ($user->chatroomsA as $chatroomA){
            $messages[$chatroomA->id] = $chatroomA->latestMessage;
        }

        foreach ($user->chatroomsB as $chatroomB){
            $messages[$chatroomB->id] = $chatroomB->latestMessage;
        }

        

        $first = DB::table('chatrooms')
            ->join('users', 'chatrooms.user_b_id', '=', 'users.id')
            ->where('chatrooms.user_a_id', '=', auth()->user()->id)
            ->select('chatrooms.id as chatroom_id', 'users.id as user_id', 'users.name', 'users.avatar');
        
        $chatrooms = DB::table('chatrooms')
            ->join('users', 'chatrooms.user_a_id', '=', 'users.id')
            ->where('chatrooms.user_b_id', '=', auth()->user()->id)
            ->select('chatrooms.id as chatroom_id', 'users.id as user_id', 'users.name', 'users.avatar')
            ->union($first)
            ->get();

        return view('chatrooms.index', [
            'user' => auth()->user(),
            'chatrooms' => $chatrooms,
            'messages' => $messages
        ]);
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
        //
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
}
