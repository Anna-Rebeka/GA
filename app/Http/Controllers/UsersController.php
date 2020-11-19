<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
     public function index(Group $group)
    {   
        $users = $group->users->except(auth()->user()->id);
        return view('profile.index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return view('profile.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('profile.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

     
    public function update(User $user){
        //TODO -> FORMREQUEST 
        $attributes = request()->validate([
            'username' => [
                'string', 
                'required', 
                'max:255', 
                'alpha_dash',
                Rule::unique('users')->ignore($user),
            ],
            'name' => [
                'string', 
                'required', 
                'max:255', 
            ],
            'email' => [
                'string', 
                'required', 
                'email',
                'max:255', 
                Rule::unique('users')->ignore($user),
            ],
            'avatar' => [
                'file'
            ],
            'banner' => [
                'file'
            ],
            'password' => [
                'nullable',
                'string', 
                'min:8',
                'max:255', 
                'confirmed'
            ]
        ]);
        
        if(request('avatar')){
            $attributes['avatar'] = request('avatar')->store('avatars');
        }

        if(request('banner')){
            $attributes['banner'] = request('banner')->store('banners');
        }
        
        if(!request('password')){
            $attributes['password'] = $user->password;
        }
        $user->update($attributes);
        return redirect($user->path());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
    }

    public function activateGroup($id){
        $user = auth()->user();
        if($user->groups->contains($id)){
            $user->active_group = $id;
            $user->save();
        }
        return redirect(auth()->user()->path());
    }
}
