<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteMemberMail;

class InviteMemberController extends Controller
{
    public function show(){
        return view('invite-member');
    }

    public function store(){
        request()->validate(['email' => 'required|email']);
        
        
        Mail::to(request('email'))
            ->send(new InviteMemberMail(auth()->user()->group->name, auth()->user()->name))
        ;
        
        
        return redirect('/invite-member')
            ->with('message', 'Email sent!');
        
    }
}
