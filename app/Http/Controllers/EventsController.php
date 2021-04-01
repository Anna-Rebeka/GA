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
use Illuminate\Support\Facades\DB;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->user()->group){
            Abort('404');
        }
        $group = auth()->user()->group;
        $events = $group->events()->with('users')->with('host')->orderBy('event_time')->where('events.event_time', '>=', now())->get();

        return view('groups.events', [
            'user' => auth()->user(),
            'group' => $group,
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

        $event = DB::transaction(function () use(&$user, &$attributes){
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
        });
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
        $notify_joined_users = $event->users()
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
        $host_id = 0;

        if($event->host){
            $name = $event->host->name;
            $host_id = $event->host->id;
        }

        return view('events.show', [
            'user' => auth()->user(),
            'going' => $event->users()->pluck('users.id'),
            'event' => $event,
            'host' => $name,
            'host_id' => $host_id
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
        if($event->group->admin_id != auth()->user()->id && $event->host_id != auth()->user()->id){
            Abort(401);
        }
        return view('events.edit', [
            'user' => auth()->user(),
            'event' => $event,
        ]);
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
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'event_time' => ['required', 'date_format:"Y-m-d\TH:i:s"'],
            'event_ending' => ['date_format:"Y-m-d\TH:i:s"', 'nullable'],
            'event_place' => ['required', 'string', 'max:255']
            ]);
    
        if($request['description'] == null){
            $attributes['description'] = null;
        }

        if($request['event_ending'] == null){
            $attributes['event_ending'] = null;
        }

        $event = DB::transaction(function () use(&$event, &$attributes){
            $event->host_id = auth()->user()->id;
            $event->update($attributes);        
            $this->updatedEventNotifyUsers($event);
            $event->users;
            return $event;
        });
        return $event;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        if($event->group->admin_id != auth()->user()->id && $event->host_id != auth()->user()->id){
            Abort(401);
        }
        DB::transaction(function () use(&$event){
            $notify_joined_users = $event->users()
                ->select('email')
                ->where('joined_event_updated_notify', true)
                ->where('users.id', '!=', auth()->user()->id)
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
        });
    }

    public function join(Event $event)
    {
        if (!$event->group->hasUser(auth()->user()))
        {
            Abort(401);
        }
        DB::transaction(function () use(&$event){
            $this->updatedEventNotifyUsers($event);
            $event->users()->attach(auth()->user()->id);
        });
    }

    public function leave(Event $event)
    {
        if (!$event->group->hasUser(auth()->user()))
        {
            Abort(401);
        }
        DB::transaction(function () use(&$event){
            $event->users()->detach(auth()->user()->id);
            $this->updatedEventNotifyUsers($event);
        });
        return auth()->user();
    }

    public function loadOlderEvents(Group $group, $howManyDisplayed)
    {
        $events = $group->events()->with('users')->with('host')->offset($howManyDisplayed)->limit(10)->orderByDesc('event_time')->get();
        return $events;
    }
}
