<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\AssignmentsComments;
use App\Events\AssignmentCommented;
use App\Events\AssignmentCommentDeleted;
use Illuminate\Support\Facades\DB;


class AssignmentsCommentsController extends Controller
{
    public function index(Assignment $assignment)
    {
        $comments = $assignment->comments()->orderBy('assignments_comments.created_at', 'desc')->with('user')->get();
        return $comments;
    }

    public function store(Request $fields){
        $attributes = $fields->validate([
            'text' => ['required', 'max:300'],
            'assignment_id' => ['required'],
        ]);
        
        $comment = DB::transaction(function () use($attributes) {
            $comment = AssignmentsComments::create([
                'user_id' => auth()->user()->id,
                'assignment_id' => $attributes['assignment_id'],
                'text' => $attributes['text'],
            ]);
            $assignment = Assignment::find($comment->assignment_id);
            broadcast(new AssignmentCommented(auth()->user(), $comment, $assignment, $assignment->group))->toOthers();
            return $comment;
        });
        return $comment;
    }

    public function destroy(Assignment $assignment, int $commentId){
        $comment = AssignmentsComments::findOrFail($commentId);
        if($assignment->group->admin_id != auth()->user()->id && $comment->user_id != auth()->user()->id){
            Abort(401);
        }
        broadcast(new AssignmentCommentDeleted(auth()->user(), $comment, $assignment, $assignment->group))->toOthers();
        $comment->delete();
        return $comment;
    }
}
