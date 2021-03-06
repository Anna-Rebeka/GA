<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteMemberMail;
use App\Models\Invite;
use App\Models\User;
use App\Models\Group;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Validator;


class InviteMemberController extends Controller
{
    public function show(Group $group){
        if($group->admin_id != auth()->user()->id){
            Abort(401);
        }
        return view('invite-member' , [
            'user' => auth()->user(),
        ]);
    }

    public function process(Request $fields, Group $group){
        if($group->admin_id != auth()->user()->id){
            Abort(401);
        }
        $validator = Validator::make($fields->all(), [
            'required|email',  
        ]);
        
        if($validator->fails()){
            return 'wrong email address';
        };

        DB::transaction(function () use(&$fields){
            $group_id = auth()->user()->group->id;
            $group = Group::find($group_id);

            $emails = $fields->all();

            foreach($emails as $invite_email){
                $user = User::where('email', $invite_email)->first();
                //if the user is already in the group inform and do nothing
                if ($user != null && $group->hasUser($user)){
                    continue;
                }
                do {
                    $token = Str::random(16);
                } 

                while (Invite::where('token', $token)->first());
                
                $invite = Invite::create([
                    'email' => $invite_email,
                    'token' => $token,
                    'group' => $group_id
                ]);
        
                Mail::to($invite_email)
                    ->send(new InviteMemberMail($group->name, auth()->user()->name, $invite))
                ;
            }
        });
        return 'All invitations sent';
    }

    public function accept($token){
        if (!$invite = Invite::where('token', $token)->first()) {
            abort(404);
        }

        //if user exists login and set active_group to this new one
        //if its a new user redirect to the dedicated registration view with active_group prefilled
        $user = User::where('email', $invite->email)->first();

        if ($user != null){
            $user = DB::transaction(function () use(&$user, &$invite){
                $user->active_group = $invite->group;
                $user->save();
                //pivot table
                $user->groups()->attach($invite->group);
                //delete all invitations for this user to this group after completing registration
                Invite::where([['email', '=', $invite->email], ['group', '=', $invite->group]])->delete();
                return $user;
            });
            return redirect($user->path());
        }

        return view('auth.invited-registration', [
            'group' => $invite->group, 
            'email' => $invite->email, 
            'token' => $token
        ]);
    }
}
