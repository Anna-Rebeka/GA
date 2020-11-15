<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class InvitedRegisterController extends Controller
{
    public function store(){
        $attributes = request()->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'active_group' => ['required']
        ]);

        $user = User::create([
            'username' => $attributes['username'],
            'name' => $attributes['name'],
            'email' => $attributes['email'],
            'password' => Hash::make($attributes['password']),
            'active_group' => $attributes['active_group']
        ]);
        
        $user->groups()->attach($attributes['active_group']);
        Invite::where([['email', '=', $attributes['email']], ['group', '=', $attributes['active_group']]])->delete();
        
        Auth::login($user);
        return redirect()->route('dashboard');
    }
}
