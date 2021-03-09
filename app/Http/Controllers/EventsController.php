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
    public function index()
    {
        $group = auth()->user()->group;
        $events = $group->events()->with('users')->with('host')->orderBy('event_time')->where('events.event_time', '>=', now())->get();

        return view('groups.events', [
            'user' => auth()->user(),
            'events' => $events
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
            'host_id' => $user->id,
            'group_id' => $user->group->id,
            'description' => $attributes['description'],
            'event_time' => $attributes['eventTime'],
            'event_place' => $attributes['eventPlace']
        ]);
        
        $user->events()->attach($event->id);
        $event->users;
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
        $event->users;
        $event->group;
        return view('events.show', [
            'user' => auth()->user(),
            'going' => $event->users()->pluck('users.id'),
            'event' => $event,
            'host' => $event->host->name
        ]);
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
    public function destroy(Event $event)
    {
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

    public function loadOlderEvents(Group $group, $howManyDisplayed)
    {
        $events = $group->events()->with('users')->with('host')->offset($howManyDisplayed)->limit(10)->orderByDesc('event_time')->get();
        return $events;
    }
}
