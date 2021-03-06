<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'avatar',
        'banner',
        'bio',
        'email',
        'password',
        'active_group',
        'got_assignment_notify',
        'my_assignment_updated_notify',
        'joined_event_updated_notify',
        'created_by_me_assignment_updated_notify',
        'created_by_me_event_updated_notify'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function path($append = ''){
        $path = route('profile', $this->username);
        return $append ? "{$path}/{$append}" : $path;
    }

    public function getAvatarAttribute($value){
        return asset($value ? 'storage/'.$value : '/img/default_avatar.png');
    }
  
    public function getBannerAttribute($value){
        return asset($value ? 'storage/'.$value : '/img/default_banner.png');
    }

    public function groups(){
        return $this->belongsToMany(Group::class)->withPivot('new_whiteboard_notify', 'new_assignment_notify', 'new_event_notify');
    }

    public function group(){
        return $this->belongsTo(Group::class,'active_group');
    }

    public function inGroup($group){
        return $this->groups->contains($group);
    }
  
    public function notes(){
        return $this->hasMany(Note::class);
    }

    public function events(){
        return $this->belongsToMany(Event::class);
    }

    public function fromMessages(){
        return $this->hasMany(Message::class, 'from_user_id');
    }

    public function toMessages(){
        return $this->hasMany(Message::class, 'to_user_id');
    }
    
    public function chatrooms() {
        return $this->belongsToMany(Chatroom::class);
    }

    public function assignments(){
        return $this->belongsToMany(Assignment::class);
    }
}
