<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        return view('groups/create-group');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attributes = request()->validate([ 
            'name' => [
                'string', 
                'required', 
                'max:255', 
            ],
        ]);
        
        
        $user = auth()->user();
        
        DB::transaction(function () use(&$user, &$attributes){
            $group = Group::create([
                'name' => $attributes['name'],
                'admin_id' => $user->id
            ]);
            
            $user->active_group = $group->id;
            $user->save();
            $user->groups()->attach($group->id);
        });

        return redirect($user->path());
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function edit(Group $group)
    {
        $user = auth()->user();
        return view('groups/edit', [
            'user' => $user,
            'group' => $user->group,
            'members' => $group->users()->orderBy('name')->get()->except($user->id),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Group $group)
    {
        $attributes = request()->validate([
            'name' => [
                'string', 
                'required', 
                'max:255', 
            ],
            'avatar' => [
                'file',
                'mimes:jpeg,jpg,png',
                'nullable',
                'max:500000',
            ],
            'admin_id' => [
                'exists:users,id',
            ],
            'board' => [
                'string', 
                'nullable', 
                'max:500', 
            ],
        ]);
    
        if(request('avatar')){
            $attributes['avatar'] = request('avatar')->store('/groups/avatars');
        }

        if(!request('board')){
            $attributes['board'] = $group->board;
        }
        $group->update($attributes);
        return back();
    }

    public function updateBoard(Request $request, Group $group)
    {
        $attributes = request()->validate([
            'board' => [
                'string', 
                'nullable', 
                'max:500', 
            ],
        ]);
            
        if(!request('board')){
            $attributes['board'] = NULL;
        }

        $group->update($attributes);
        return $group->board;
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Group  $group
     * @return \Illuminate\Http\Response
     */
    public function destroy(Group $group)
    {
        //
    }

    public function getMembers(Group $group){
        return $group->users->except(auth()->user()->id);
    }

    public function showWhiteboard(){
        return view('groups.group-whiteboard', [
            'user' => auth()->user(),
            'group' => auth()->user()->group,
        ]);
    }
}
