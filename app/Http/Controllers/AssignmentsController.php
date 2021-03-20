<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewWhiteboardEventAssignmentMail;
use App\Mail\FromAllGroupsNotificationMail;
use App\Mail\DeletedNotificationMail;


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
        $assignments = $group->assignments()->with('users')->with('author')->where('due', '>=', now())->orderBy('due')->get();

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
            'duration_hours' => ['integer', 'min:0'],
            'duration_minutes' => ['integer', 'min:0', 'max:59'], 
            'due' => ['required'],
            'max_assignees' => ['integer'] 
            ]);
        
        
        $attributes['duration'] = '';
        
        if($fields['duration_hours'] != null && $fields['duration_hours'] > 0){
            $attributes['duration'] = $attributes['duration'] . ' ' . $fields['duration_hours'] . 'h';
        }

        if($fields['duration_minutes'] != null && $fields['duration_minutes'] > 0){
            $attributes['duration'] =  $attributes['duration'] . ' ' . $fields['duration_minutes'] . 'min';
        }
        
        if($attributes['duration'] == ''){
            $attributes['duration'] = null;
        }
        
        $user = auth()->user();

        if($fields['assignee'] == null){
            $attributes['assignee'] = null;
        }

        if($fields['max_assignees'] == 0){
            $attributes['max_assignees'] = null;
        }

        $assignment = Assignment::create([
            'name' => $attributes['name'],
            'author_id' => $user->id,
            'group_id' => $user->group->id,
            'description' => $attributes['description'],
            'max_assignees' =>  $attributes['max_assignees'],
            'on_time' => $attributes['on_time'],
            'duration' => $attributes['duration'],
            'due' => $attributes['due'],
        ]);

        //for each assignee attach this assignment
        $assignment->users()->attach($fields['users']);
        //notify users
        $this->newAssignmentNotifyUsers($assignment);

        $assignment->users;
        
        return $assignment;
    }


    public function newAssignmentNotifyUsers(Assignment $assignment){
        $notify_members = $assignment->group->users()->select('email')->where('new_assignment_notify', true)->where('users.id', '!=', auth()->user()->id)->get();
        $notify_assignees = $assignment->users()->select('email')->where('got_assignment_notify', true)->where('users.id', '!=', auth()->user()->id)->get();
        
        Mail::to($notify_members)
                ->send(new NewWhiteboardEventAssignmentMail($assignment->group->name, 'assignments', $assignment))
        ;
        Mail::to($notify_assignees)
                ->send(new FromAllGroupsNotificationMail($assignment->group->name, 'You have a new assignment', 'assignments/' . $assignment->id))
        ;
        return;
    }


    public function updatedAssignmentNotifyUsers(Assignment $assignment){
        if($assignment->author && auth()->user()->id != $assignment->author->id && $assignment->author->created_by_me_assignment_updated_notify){
            Mail::to($assignment->author->email)
                ->send(new FromAllGroupsNotificationMail($assignment->group->name, 'Update on the "' . $assignment->name . '" assignment', 'assignments/' . $assignment->id))
        ;
        }
        $author_email = ''; 
        if($assignment->author){
            $author_email = $assignment->author->email;
        }
        $notify_assignees = $assignment->users()
            ->select('email')
            ->where('my_assignment_updated_notify', true)
            ->where('users.id', '!=', auth()->user()->id)
            ->where('users.email', '!=', $author_email)
            ->get();
        Mail::to($notify_assignees)
            ->send(new FromAllGroupsNotificationMail($assignment->group->name, 'Update on the "' . $assignment->name . '" assignment', 'assignments/' . $assignment->id))
        ;
        return;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function show(Assignment $assignment)
    {
        $author = "*deleted account*";
        if($assignment->author){
            $author = $assignment->author->name;
        }

        $assignment->users;

        $assignment->taken = $assignment->isAssigned(auth()->user());

        return view('assignments.show', [
            'user' => auth()->user(),
            'assignment' => $assignment,
            'author' => $author,
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
        $notify_assignees = $assignment->users()->select('email')->where('my_assignment_updated_notify', true)->where('users.id', '!=', auth()->user()->id)->get();
        Mail::to($notify_assignees)
                ->send(new DeletedNotificationMail(
                    $assignment->group->name, 
                    'Your assignment "' . $assignment->name . '" has been deleted', 
                    $assignment->author->name, 
                    'profile/' . $assignment->author->username
                ))
        ;
        $assignment->users()->detach();
        $assignment->delete();
    }
    
    public function take(Assignment $assignment)
    {
        $assignment->users()->attach(auth()->user()->id);
        $this->updatedAssignmentNotifyUsers($assignment);
    }

    public function done(Assignment $assignment)
    {
        $assignment->update(array('done' => true));
        $this->updatedAssignmentNotifyUsers($assignment);
    }

    public function loadOlderAssignments(Group $group, $howManyDisplayed){
        $assignments = $group->assignments()->with('users')->with('author')->offset($howManyDisplayed)->limit(10)->orderByDesc('due')->get();
        return $assignments;
    }
}

