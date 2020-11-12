<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteMemberMail;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Support\Str;

class InviteMemberController extends Controller
{
    public function show(){
        return view('invite-member');
    }

    public function process(){
        request()->validate(['email' => 'required|email']);
        $groupId = auth()->user()->group->id;
        
        do {
            $token = Str::random(16);
        } 
        while (Invite::where('token', $token)->first());
        
        $invite = Invite::create([
            'email' => request()->get('email'),
            'token' => $token,
            'group' => $groupId
        ]);

        Mail::to(request('email'))
            ->send(new InviteMemberMail($groupId, auth()->user()->name, $invite))
        ;
    
        return redirect('/invite-member')
            ->with('message', 'Email sent!');
        
    }

    public function accept($token){
        if (!$invite = Invite::where('token', $token)->first()) {
            abort(404);
        }

        Invite::where([['email', '=', $invite->email], ['group', '=', $invite->group]])->delete();
        
        //redirect to dedicated registration view with active_group prefilled
        
        //User::create(['email' => $invite->email, 'active_group' => $invite->group]);
        //    $invite->delete();
        
        return 'Good job! Invite accepted!';
        }
}
