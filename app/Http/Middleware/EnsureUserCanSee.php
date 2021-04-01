<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Group;
use App\Models\Chatroom;
use App\Models\Assignment;
use App\Models\Event;
use Illuminate\Http\Request;

class EnsureUserCanSee
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->group){
            $group = null;
            if(is_a($request->group, 'App\Models\Group')){      
                $group = $request->group;
            }
            else{
                $group = Group::find($request->group);
            }
            if(!$group->hasUser(auth()->user())){
                Abort('401');
            }
        }
        if($request->event){
            $event = null;
            if(is_a($request->event, 'App\Models\Event')){      
                $event = $request->event;
            }
            else{
                $event = Event::find($request->event);
            }
            if(!$event->group->hasUser(auth()->user())){
                Abort('401');
            }
        }
        if($request->assignment){
            $assignment = null;
            if(is_a($request->assignment, 'App\Models\Assignment')){      
                $assignment = $request->assignment;
            }
            else{
                $assignment = Assignment::find($request->assignment);
            }
            if(!$assignment->group->hasUser(auth()->user())){
                Abort('401');
            }
        }
        return $next($request);
    }
}
