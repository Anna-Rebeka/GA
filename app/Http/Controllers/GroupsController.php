<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

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

        $group = Group::create([
            'name' => $attributes['name'],
            'admin_id' => $user->id
        ]);
        
        $user->active_group = $group->id;
        $user->save();
        $user->groups()->attach($group->id);

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
        //
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
            'admin_id' => [
                'exists:users,id',
                'required',
            ],
            'board' => [
                'string', 
                'nullable', 
                'max:500', 
            ],
        ]);
    
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

    public function showWhiteboard(Group $group){
        return view('groups.group-whiteboard', [
            'user' => auth()->user(),
            'group' => $group,
        ]);
    }
}
