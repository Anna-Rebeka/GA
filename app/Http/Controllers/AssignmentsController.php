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
    public function index(Group $group)
    {
        $assignments = $group->assignments()->orderBy('due')->get();

        foreach ($assignments as $assignment){
            $assignment->assignee;
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
            'description' => ['string', 'max:255'],
            'due' => ['required'],
            'assignee' => ['nullable', 'integer']
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
            'due' => $attributes['due'],
            'assignee_id' => $attributes['assignee']
        ]);

        if($assignment->assignee_id){
            $assignment->assignee;
        }
        
        return $assignment;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function show(Group $group, Assignment $assignment)
    {
        $assignment->author;
        $assignment->assignee;
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
        $assignment->delete();
    }
    
    public function take(Assignment $assignment)
    {
        $assignment->update(array('assignee_id' => auth()->user()->id));
    }
}

