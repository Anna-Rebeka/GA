<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Group $group)
    {

        $events = $group->events()->orderBy('event_time')->get();
        $eusers = [];

        foreach ($events as $event){
            $event->users;
            $eusers[$event->id] = $event->users->pluck('id');
            $event->author;
        }

        return view('groups.events', [
            'user' => auth()->user(),
            'events' => $events,
            'eusers' => $eusers,
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
    public function store(Request $fields)
    {
        $attributes = $fields->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'eventTime' => ['required'],
            'eventPlace' => ['required', 'string', 'max:255']
            ]);
        
        $user = auth()->user();

        if($fields['description'] == null){
            $attributes['description'] = null;
        }
        
        $event = Event::create([
            'name' => $attributes['name'],
            'author_id' => $user->id,
            'group_id' => $user->group->id,
            'description' => $attributes['description'],
            'event_time' => $attributes['eventTime'],
            'event_place' => $attributes['eventPlace']
        ]);
        
        $user->events()->attach($event->id);
        return $event;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($group, int $id)
    {
        $event = Event::findOrFail($id);
        $event->users()->detach();
        $event->delete();
    }

    public function join(Event $event)
    {
        $event->users()->attach(auth()->user()->id);
    }

    public function leave(Event $event)
    {
        $event->users()->detach(auth()->user()->id);
    }
}
