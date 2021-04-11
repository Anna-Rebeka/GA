<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
     public function index(Group $group)
    {   
        $users = $group->users()->orderBy('name')->where('users.id', '!=', auth()->user()->id)->get();
        return view('profile.index', [
            'users' => $users,
            'group' => $group
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
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        
        return view('profile.show', [
            'auth_user' => auth()->user(),
            'user' => $user,
            'user_created_at' => $user->created_at->diffForHumans(),
            'user_edit_path' => $user->path('edit')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('profile.edit', [
            'user' => $user,
            'user_path' => $user->path(),
        ]);
    }


    public function showSettings(User $user)
    {
        $group = $user->group;
        $new_whiteboard_notify = 0;
        $new_assignment_notify = 0;
        $new_event_notify = 0;

        if($user->group){
            $user->new_whiteboard_notify = $user->groups()->where('groups.id', $group->id)->first()->pivot->new_whiteboard_notify;
            $user->new_assignment_notify = $user->groups()->where('groups.id', $group->id)->first()->pivot->new_assignment_notify;
            $user->new_event_notify = $user->groups()->where('groups.id', $group->id)->first()->pivot->new_event_notify;
        }

        return view('profile.settings', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

    public function updateSettings(User $user){
        $user_attributes = request()->validate([
            'got_assignment_notify' => [
                'boolean', 
                'required',
            ],
            'my_assignment_updated_notify' => [
                'boolean', 
                'required', 
            ],
            'joined_event_updated_notify' => [
                'boolean', 
                'required',
            ],
            'created_by_me_assignment_updated_notify' => [
                'boolean', 
                'required',
            ],
            'created_by_me_event_updated_notify' => [
                'boolean', 
                'required',
            ],
        ]);
        
        
        $pivot_attributes = request()->validate([
            'new_whiteboard_notify' => [
                'boolean', 
                'required',
            ],
            'new_assignment_notify' => [
                'boolean', 
                'required', 
            ],
            'new_event_notify' => [
                'boolean', 
                'required',
            ],
        ]);
        
        
        $user = DB::transaction(function () use(&$user, &$user_attributes, &$pivot_attributes){
            $user->update($user_attributes);
            $user->groups()->updateExistingPivot($user->group->id, $pivot_attributes);
            return $user;
        });
        return $user;
    }

     
    public function update(Request $request, User $user){
        $attributes = request()->validate([
            'username' => [
                'string', 
                'required', 
                'max:255', 
                'alpha_dash',
                Rule::unique('users')->ignore($user),
            ],
            'name' => [
                'string', 
                'required', 
                'max:255', 
            ],
            'email' => [
                'string', 
                'required', 
                'email',
                'max:255', 
                Rule::unique('users')->ignore($user),
            ],
            'avatar' => [
                'file',
                'mimes:jpeg,jpg,png',
                'nullable',
                'max:500000',
            ],
            'banner' => [
                'file',
                'nullable',
                'mimes:jpeg,jpg,png',
                'nullable',
                'max:500000',
            ],
            'bio' => [
                'string', 
                'nullable', 
                'max:150', 
            ],
            'password' => [
                'nullable',
                'string', 
                'min:8',
                'max:255', 
                'confirmed'
            ]
        ]);
        
        if(request('password')){
            $attributes['password'] = Hash::make(request('password'));
        }

        if(request('avatar')){
            $attributes['avatar'] = request('avatar')->store('/users/avatars');
        }

        if(request('banner')){
            $attributes['banner'] = request('banner')->store('/users/banners');
        }
        
        $user->update($attributes);
        Auth::login($user);
        return $user->path();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        if (auth()->user()->id != $user->id){
            Abort(401);
        }
        $user->delete();
    }

    public function activateGroup($id){
        $user = auth()->user();
        if($user->groups->contains($id)){
            $user->active_group = $id;
            $user->save();
        }
    }

    public function getActiveGroup(User $user){
        return $user->group;
    }
    
    public function getAllUsersGroups(User $user){
        if(!$user->group){
            return $user->groups()->orderBy('name')->get();
        }
        return $user->groups()->orderBy('name')->get()->except($user->group->id);
    }

    public function getAllUserEvents(User $user){
        $user_with_events = User::where('users.id', $user->id)
            ->join('group_user', 'users.id', '=', 'group_user.user_id')
            ->join('groups', 'group_user.group_id', '=', 'groups.id')
            ->join('events', 'groups.id', '=', 'events.group_id')
            ->select('events.*', 'groups.name as group_name')
            ->where('events.event_time', '>=', now())
            ->orderBy('events.event_time')
            ->get();
        return $user_with_events;
    }

    public function getUserJoinedEvents(User $user){
        $user_with_joined_events = User::where('users.id', $user->id)
            ->join('event_user', 'users.id', '=', 'event_user.user_id')
            ->join('events', 'event_user.event_id', '=', 'events.id')
            ->Leftjoin('groups', 'events.group_id', '=', 'groups.id')
            ->select('events.*', 'groups.name as group_name')
            ->where('events.event_time', '>=', now())
            ->orderBy('events.event_time')
            ->get();
        return $user_with_joined_events;
    }

    public function getAllUsersAssignments(User $user){
        $user_with_assignments = User::where('users.id', $user->id)
            ->join('group_user', 'users.id', '=', 'group_user.user_id')
            ->join('groups', 'group_user.group_id', '=', 'groups.id')
            ->join('assignments', 'groups.id', '=', 'assignments.group_id')
            ->leftJoin('users as author', 'assignments.author_id', '=', 'author.id')
            ->select('assignments.*', 'groups.name as group_name', 'author.id as author_id', 'author.name as author_name')
            ->where('assignments.due', '>=', now())
            ->orderBy('assignments.due')
            ->get();
        return $user_with_assignments;
    }

    public function getUsersAssignments(User $user){
        return $user->assignments()->with('group')->with('author')->where('assignments.due', '>=', now())
        ->get();
    }
}
