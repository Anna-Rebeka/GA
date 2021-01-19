<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\AssignmentsComments;

class AssignmentsCommentsController extends Controller
{
    public function index(Assignment $assignment)
    {
        $comments = $assignment->comments()->orderBy('assignments_comments.created_at', 'desc')->get();
        foreach ($comments as $comment){
            $comment->user;
        }
        return $comments;
    }

    public function store(Request $fields){
        $attributes = $fields->validate([
            'text' => ['required', 'max:300'],
            'assignment_id' => ['required'],
            ]);
        $comment = AssignmentsComments::create([
            'user_id' => auth()->user()->id,
            'assignment_id' => $attributes['assignment_id'],
            'text' => $attributes['text'],
        ]);

        $event = Assignment::find($comment->event_id);
        //broadcast(new EventCommented(auth()->user(), $comment, $event, $event->group))->toOthers();

        return $comment;
    }
}
