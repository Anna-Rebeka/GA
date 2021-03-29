<?php

namespace App\Http\Controllers;

use App\Models\GroupWhiteboardPost;
use App\Models\Group;

use App\Events\NewWhiteboardPost;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewWhiteboardEventAssignmentMail;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class GroupWhiteboardPostsController extends Controller
{
    public function getPosts(Group $group)
    {
        return GroupWhiteboardPost::where('group_id', $group->id)
        ->with('sender')
        ->orderBy('created_at', 'DESC')
        ->limit(30)
        ->get();    
    }

     /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $fields)
    {
        if($fields->image){
            $fields->image = $fields->file('image');
        }
        if($fields->file){
            $fields->file = $fields->file('file');
        }

        $attributes = $fields->validate([
            'group_id' => ['required'],
            'text' => ['nullable', 'max:1000'],
            'image' => ['nullable', 'file', 'mimes:jpeg,jpg,png', 'max:500000'],
            'file' => ['nullable', 'file', 'max:2500000'],
            'file_name' => ['nullable', 'string', 'max:1000'],
            ]);
        
        if(!isset($attributes['image']) || empty($attributes['image'])){
            $attributes['image'] = null;
        }
        else {
            $attributes['image'] = $fields->image->store('posts');
        }
        if(!isset($attributes['file']) || empty($attributes['file'])){
            $attributes['file'] = null;
        }
        else{
            $attributes['file'] = $fields->file->store('posts');
        }

        if(!isset($attributes['file_name']) || empty($attributes['file_name'])){
            $attributes['file_name'] = null;
        }
        else{
            $ext = pathinfo($attributes['file'], PATHINFO_EXTENSION);
            $attributes['file_name'] .= '.' . $ext;
        }

        $post = DB::transaction(function () use(&$attributes){
            $post = GroupWhiteboardPost::create([
                'sender_id' => auth()->user()->id,
                'group_id' => $attributes['group_id'],
                'text' => $attributes['text'],
                'image_path' => $attributes['image'],
                'file_path' => $attributes['file'],
                'file_name' => $attributes['file_name'],
            ]);
                    
            broadcast(new NewWhiteboardPost($post, Group::find($post->group_id), auth()->user()))->toOthers();
            $this->notifyUsers($post);
            $post->sender;
            return $post;
        });

        return $post;
    }


    public function notifyUsers(GroupWhiteboardPost $post){
        $notify_users = $post->group->users()->where('new_whiteboard_notify', true)->get();
        foreach($notify_users as $notify_user){
            Mail::to($notify_user->email)
                ->send(new NewWhiteboardEventAssignmentMail($post->group->name, 'whiteboard posts', $post))
            ;
        }
        return;
    }


    public function destroy(Group $group, $post_id)
    {
        DB::transaction(function () use(&$group, &$post_id){
            $post = GroupWhiteboardPost::findOrFail($post_id);

            if($post->file_path){
                Storage::delete($post->file_path);
            }
            if($post->image_path){
                Storage::delete($post->image_path);
            }

            $post->delete();
        });
    }

    public function loadOlderPosts(Group $group, $howManyDisplayed){
        return GroupWhiteboardPost::where('group_id', $group->id)
            ->with('sender')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->offset($howManyDisplayed)
            ->get();
    }
}
