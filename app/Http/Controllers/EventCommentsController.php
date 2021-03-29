<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Events\EventCommented;


class EventCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Event $event)
    {
        $comments = $event->comments()->orderBy('event_comments.created_at', 'desc')->get();
        foreach ($comments as $comment){
            $comment->user;
        }
        return $comments;
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
    public function store(Request $fields){
        $attributes = $fields->validate([
            'text' => ['required', 'max:300'],
            'event_id' => ['required'],
            ]);

        $comment = DB::transaction(function () use(&$attributes){
            $comment = EventComment::create([
                'user_id' => auth()->user()->id,
                'event_id' => $attributes['event_id'],
                'text' => $attributes['text'],
            ]);

            $event = Event::find($comment->event_id);
            broadcast(new EventCommented(auth()->user(), $comment, $event, $event->group))->toOthers();
            return $comment;
        });
        return $comment;
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EventComment  $eventComments
     * @return \Illuminate\Http\Response
     */
    public function show(EventComment $eventComments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EventComment  $eventComments
     * @return \Illuminate\Http\Response
     */
    public function edit(EventComment $eventComments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EventComment  $eventComments
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventComment $eventComments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventComment  $eventComments
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventComment $eventComments)
    {
        //
    }
}
