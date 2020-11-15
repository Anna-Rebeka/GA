<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteMemberMail;
use App\Models\Invite;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Str;

class InviteMemberController extends Controller
{
    public function show(){
        return view('invite-member');
    }

    public function process(){
        request()->validate(['email' => 'required|email']);
        $groupId = auth()->user()->group->id;
        $group = Group::find($groupId);
        $userEmail = request()->get('email');
        $user = User::where('email', $userEmail)->first();
        
        //if the user is already in the group inform and do nothing
        if ($user != null && $group->hasUser($user)){
            $userExists = true;
            return view('invite-member', ['userExists' => $userExists]);
        }

        do {
            $token = Str::random(16);
        } 
        while (Invite::where('token', $token)->first());
        
        $invite = Invite::create([
            'email' => $userEmail,
            'token' => $token,
            'group' => $groupId
        ]);

        Mail::to(request('email'))
            ->send(new InviteMemberMail($group->name, auth()->user()->name, $invite))
        ;
    
        return redirect('/invite-member')
            ->with('message', 'Email sent!');
        
    }

    public function accept($token){
        if (!$invite = Invite::where('token', $token)->first()) {
            abort(404);
        }

        //if user exists login and set active_group to this new one
        //if its a new user redirect to the dedicated registration view with active_group prefilled
        $user = User::where('email', $invite->email)->first();

        if ($user != null){
            $user->active_group = $invite->group;
            $user->save();
            //pivot table
            $user->groups()->attach($invite->group);
            //delete all invitations for this user to this group after completing registration
            Invite::where([['email', '=', $invite->email], ['group', '=', $invite->group]])->delete();
            return redirect($user->path());
        }

        return view('auth.invited-registration', [
            'group' => $invite->group, 
            'email' => $invite->email, 
            'token' => $token
        ]);
    }
}
