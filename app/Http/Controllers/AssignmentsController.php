<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Group;
use App\Models\User;


use Illuminate\Http\Request;

class AssignmentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $group = auth()->user()->group;
        $assignments = $group->assignments()->orderBy('due')->get();

        foreach ($assignments as $assignment){
            $assignment->users;
            $assignment->author;
        }

        return view('groups.assignments', [
            'user' => auth()->user(),
            'assignments' => $assignments,
            'members' => $group->users
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
            'description' => ['required', 'string', 'max:255'],
            'on_time' => ['required', 'boolean'],
            'due' => ['required'],
            ]);
        
        $user = auth()->user();

        if($fields['assignee'] == null){
            $attributes['assignee'] = null;
        }

        $assignment = Assignment::create([
            'name' => $attributes['name'],
            'author_id' => $user->id,
            'group_id' => $user->group->id,
            'description' => $attributes['description'],
            'on_time' => $attributes['on_time'],
            'due' => $attributes['due'],
        ]);

        //for each assignee attach this assignment
        $assignment->users()->attach($fields['users']);

        //if assignment has assignee(s) return them as assignment->users
        if(true){
            $assignment->users;
        }
        
        return $assignment;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function show(Assignment $assignment)
    {
        $assignment->author;
        $assignment->users;
        return view('assignments.show', [
            'user' => auth()->user(),
            'assignment' => $assignment
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function edit(Assignment $assignment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Assignment $assignment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Assignment $assignment)
    {
        $assignment->users()->detach();
        $assignment->delete();
    }
    
    public function take(Assignment $assignment)
    {
        $assignment->users()->attach(auth()->user()->id);
    }

    public function done(Assignment $assignment)
    {
        $assignment->update(array('done' => true));
    }
}

