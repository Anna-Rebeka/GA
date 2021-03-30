<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Group;
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
            if(!$request->event->group->hasUser(auth()->user())){
                Abort('401');
            }
        }
        if($request->assignment){
            if(!$request->assignment->group->hasUser(auth()->user())){
                Abort('401');
            }
        }
        if($request->chatroom){
            if(!$request->chatroom->hasUser(auth()->user())){
                Abort('401');
            }
        }
        return $next($request);
    }
}
