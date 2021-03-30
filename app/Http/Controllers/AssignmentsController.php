<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewWhiteboardEventAssignmentMail;
use App\Mail\FromAllGroupsNotificationMail;
use App\Mail\DeletedNotificationMail;
use Illuminate\Support\Facades\DB;


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
        if(!auth()->user()->group){
            Abort('404');
        }
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

        if($fields['max_assignees'] == 0){
            $attributes['max_assignees'] = null;
        }

        $assignment = DB::transaction(function () use(&$assignment, &$user, $attributes, &$fields){
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
        });
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
        $assignment->group;
        $assignment->taken = $assignment->isAssigned(auth()->user());

        return view('assignments.show', [
            'user' => auth()->user(),
            'assignment' => $assignment,
            'author' => $author,
            'assignment_users_ids' => $assignment->users->pluck('id'),
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
        if($assignment->group->admin_id != auth()->user()->id && $assignment->author_id != auth()->user()->id){
            Abort(401);
        }
        $assignment->users;
        $free_members = $assignment->group->users->diff($assignment->users);
        return view('assignments.edit', [
            'user' => auth()->user(),
            'assignment' => $assignment,
            'group' => $assignment->group,
            'free_members' => $free_members,
        ]);
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
        if($assignment->group->admin_id != auth()->user()->id && $assignment->author_id != auth()->user()->id){
            Abort(401);
        }
        $attributes = request()->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'on_time' => ['required', 'boolean'],
            'duration_hours' => ['integer', 'min:0', 'nullable'],
            'duration_minutes' => ['integer', 'min:0', 'max:59', 'nullable'], 
            'due' => ['required'],
            'max_assignees' => ['integer', 'nullable'] 
            ]);
        
        
        $attributes['duration'] = '';
        
        if($request['duration_hours'] != null && $request['duration_hours'] > 0){
            $attributes['duration'] = $attributes['duration'] . ' ' . $request['duration_hours'] . 'h';
        }

        if($request['duration_minutes'] != null && $request['duration_minutes'] > 0){
            $attributes['duration'] =  $attributes['duration'] . ' ' . $request['duration_minutes'] . 'min';
        }
        
        if($attributes['duration'] == ''){
            $attributes['duration'] = null;
        }

        if($request['max_assignees'] == 0){
            $attributes['max_assignees'] = null;
        }
        
        $assignment = DB::transaction(function () use(&$assignment, &$attributes) {
            $user = auth()->user();
            $assignment->update($attributes);
            $users_before = $assignment->users;
            $assignment->users()->detach();
            $assignment->users()->attach($request['users']);
            $all_users_emails =  $assignment->users->merge($users_before)->where('got_assignment_notify', true)->pluck('email');
            Mail::to($all_users_emails)
                    ->send(new FromAllGroupsNotificationMail($assignment->group->name, 'Update on the "' . $assignment->name . '" assignment', 'assignments/' . $assignment->id))
            ;
            return $assignment;
        });
        return $assignment;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Assignment  $assignment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Assignment $assignment)
    {
        if($assignment->group->admin_id != auth()->user()->id && $assignment->author_id != auth()->user()->id){
            Abort(401);
        }
        DB::transaction(function () use(&$assignment) {
            $notify_assignees = $assignment->users()->select('email')->where('my_assignment_updated_notify', true)->where('users.id', '!=', auth()->user()->id)->get();
            
            $author_name = "";
            $author_url = "";
            
            if($assignment->author){
                $author_name = $assignment->author->name;
                $author_url = 'profile/' . $assignment->author->username;
            }
            Mail::to($notify_assignees)
                    ->send(new DeletedNotificationMail(
                        $assignment->group->name, 
                        'Your assignment "' . $assignment->name . '" has been deleted', 
                        $author_name,
                        $author_url
                    ))
            ;
            $assignment->users()->detach();
            $assignment->delete();
        });
    }
    
    public function take(Assignment $assignment)
    {
        if ($assignment->group->admin_id != auth()->user()->id && 
            $assignment->author_id != auth()->user()->id ||
            !$assignment->group->hasUser(auth()->user())
            )
        {
            Abort(401);
        }
        DB::transaction(function () use(&$assignment) {
            $this->updatedAssignmentNotifyUsers($assignment);
            $assignment->users()->attach(auth()->user()->id);
        });
    }

    public function done(Assignment $assignment)
    {
        if($assignment->group->admin_id != auth()->user()->id && $assignment->author_id != auth()->user()->id){
            Abort(401);
        }
        DB::transaction(function () use(&$assignment) {
            $assignment->update(array('done' => true));
            $this->updatedAssignmentNotifyUsers($assignment);
        });
    }

    public function loadOlderAssignments(Group $group, $howManyDisplayed){
        $assignments = $group->assignments()->with('users')->with('author')->offset($howManyDisplayed)->limit(10)->orderByDesc('due')->get();
        return $assignments;
    }
}

