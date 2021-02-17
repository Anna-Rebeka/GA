<?php

namespace App\Http\Controllers;

use App\Models\GroupWhiteboardPost;
use App\Models\Group;

use App\Events\NewWhiteboardPost;

use Illuminate\Http\Request;

class GroupWhiteboardPostsController extends Controller
{
    public function get_posts(Group $group)
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
            'image' => ['nullable', 'file'],
            'file' => ['nullable', 'file'],
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

        $post = GroupWhiteboardPost::create([
            'sender_id' => auth()->user()->id,
            'group_id' => $attributes['group_id'],
            'text' => $attributes['text'],
            'image_path' => $attributes['image'],
            'file_path' => $attributes['file'],
            'file_name' => $attributes['file_name'],
        ]);
                
        broadcast(new NewWhiteboardPost($post, Group::find($post->group_id), auth()->user()))->toOthers();
        
        $post->sender;

        return $post;
    }


    public function load_older_posts(Group $group, $howManyDisplayed){
        return GroupWhiteboardPost::where('group_id', $group->id)
            ->with('sender')
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->offset($howManyDisplayed)
            ->get();
    }
}
