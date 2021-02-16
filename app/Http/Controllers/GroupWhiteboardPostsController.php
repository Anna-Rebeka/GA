<?php

namespace App\Http\Controllers;

use App\Models\GroupWhiteboardPost;
use App\Models\Group;

use Illuminate\Http\Request;

class GroupWhiteboardPostsController extends Controller
{
    public function get_posts(Group $group)
    {
        return GroupWhiteboardPost::where('group_id', $group->id)
        ->with('user')
        ->orderBy('created_at', 'DESC')
        ->limit(30)
        ->get();    
    }

    
}
