<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'users' => $users
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

     
    public function update(User $user){
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
                'file'
            ],
            'banner' => [
                'file'
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
        
        if(request('avatar')){
            $attributes['avatar'] = request('avatar')->store('/users/avatars');
        }

        if(request('banner')){
            $attributes['banner'] = request('banner')->store('/users/banners');
        }
        
        if(!request('password')){
            $attributes['password'] = $user->password;
        }
        $user->update($attributes);
        return redirect($user->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function activateGroup($id){
        $user = auth()->user();
        if($user->groups->contains($id)){
            $user->active_group = $id;
            $user->save();
        }
    }
    
    public function getAllUsersGroups(User $user){
        if(!$user->group){
            return [];
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
