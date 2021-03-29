<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Chatroom;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use Illuminate\Support\Facades\DB;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Chatroom $chatroom)
    {
        //
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
    public function store(Request $fields)
    {
        if($fields->image){
            $fields->image = $fields->file('image');
        }
        if($fields->file){
            $fields->file = $fields->file('file');
        }

        $attributes = $fields->validate([
            'chatroom_id' => ['required'],
            'text' => ['nullable', 'max:1000'],
            'image' => ['nullable', 'file'],
            'file' => ['nullable', 'file'],
            'file_name' => ['nullable', 'string', 'max:1000'],
            ]);
        
        if(!isset($attributes['image']) || empty($attributes['image'])){
            $attributes['image'] = null;
        }
        else {
            $attributes['image'] = $fields->image->store('photos');
            ImageOptimizer::optimize('storage/' . $attributes['image']);
        }
        if(!isset($attributes['file']) || empty($attributes['file'])){
            $attributes['file'] = null;
        }
        else{
            $attributes['file'] = $fields->file->store('files');
        }

        if(!isset($attributes['file_name']) || empty($attributes['file_name'])){
            $attributes['file_name'] = null;
        }
        else{
            $ext = pathinfo($attributes['file'], PATHINFO_EXTENSION);
            $attributes['file_name'] .= '.' . $ext;
        }

        $message = DB::transaction(function () use(&$attributes){
            $message = Message::create([
                'sender_id' => auth()->user()->id,
                'chatroom_id' => $attributes['chatroom_id'],
                'text' => $attributes['text'],
                'image_path' => $attributes['image'],
                'file_path' => $attributes['file'],
                'file_name' => $attributes['file_name'],
                'read' => false,
            ]);
            
            broadcast(new MessageSent($message, Chatroom::find($message->chatroom_id), auth()->user()))->toOthers();
            return $message;
        });
        return $message;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
