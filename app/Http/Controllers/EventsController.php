<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewWhiteboardEventAssignmentMail;
use App\Mail\FromAllGroupsNotificationMail;
use App\Mail\DeletedNotificationMail;

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
            'eventTime' => ['required', 'date_format:"Y-m-d\TH:i"'],
            'eventEnding' => ['date_format:"Y-m-d\TH:i"'],
            'eventPlace' => ['required', 'string', 'max:255']
            ]);
        
        $user = auth()->user();
        
        if($fields['description'] == null){
            $attributes['description'] = null;
        }

        if($fields['eventEnding'] == null){
            $attributes['eventEnding'] = null;
        }

        $event = Event::create([
            'name' => $attributes['name'],
            'host_id' => $user->id,
            'group_id' => $user->group->id,
            'description' => $attributes['description'],
            'event_time' => $attributes['eventTime'],
            'event_ending' => $attributes['eventEnding'],
            'event_place' => $attributes['eventPlace']
        ]);
        
        $user->events()->attach($event->id);
        $this->newEventNotifyUsers($event);
        $event->users;
        return $event;
    }


    public function newEventNotifyUsers(Event $event){
        $notify_members = $event->group->users()->select('email')->where('new_event_notify', true)->where('users.id', '!=', auth()->user()->id)->get();
        Mail::to($notify_members)
                ->send(new NewWhiteboardEventAssignmentMail($event->group->name, 'events', $event))
        ;
        return;
    }

    public function updatedEventNotifyUsers(Event $event){
        if($event->host && auth()->user()->id != $event->host->id && $event->host->created_by_me_event_updated_notify){
            Mail::to($event->host->email)
                ->send(new FromAllGroupsNotificationMail($event->group->name, 'Update on the "' . $event->name . '" event', 'events/' . $event->id))
            ;
        }
        $host_email = ''; 
        if($event->host){
            $host_email = $event->host->email;
        }
        $notify_joined_users = $host->users()
            ->select('email')
            ->where('joined_event_updated_notify', true)
            ->where('users.id', '!=', auth()->user()->id)
            ->where('users.email', '!=', $host_email)
            ->get();
        Mail::to($notify_joined_users)
            ->send(new FromAllGroupsNotificationMail($event->group->name, 'Update on the "' . $event->name . '" event', 'events/' . $event->id))
        ;
        return;
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
        $name = "*deleted account*";

        if($event->host){
            $name = $event->host->name;
        }

        return view('events.show', [
            'user' => auth()->user(),
            'going' => $event->users()->pluck('users.id'),
            'event' => $event,
            'host' => $name
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
        $notify_joined_users = $host->users()
            ->select('email')
            ->where('joined_event_updated_notify', true)
            ->where('users.id', '!=', auth()->user()->id)
            ->where('users.email', '!=', $host_email)
            ->get();
        
        $host_name = "";
        $host_url = "";
        
        if($event->host){
            $host_name = $event->host->name;
            $host_url = 'profile/' . $event->host->username;
        }

        Mail::to($notify_joined_users)
                ->send(new DeletedNotificationMail(
                    $event->group->name, 
                    'Your event "' . $event->name . '" has been deleted', 
                    $host_name, 
                    $host_url
                ))
        ;
        $event->users()->detach();
        $event->delete();
    }

    public function join(Event $event)
    {
        $this->updatedEventNotifyUsers($event);
        $event->users()->attach(auth()->user()->id);
    }

    public function leave(Event $event)
    {
        $event->users()->detach(auth()->user()->id);
        $this->updatedEventNotifyUsers($event);
    }

    public function loadOlderEvents(Group $group, $howManyDisplayed)
    {
        $events = $group->events()->with('users')->with('host')->offset($howManyDisplayed)->limit(10)->orderByDesc('event_time')->get();
        return $events;
    }
}
