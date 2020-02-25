<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Admin extends Authenticatable
{
    protected $fillable = ['username', 'email', 'password'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function post()
    {
        return $this->hasMany('App\Models\Post');
    }

    public function sponsoredPost()
    {
        return $this->hasMany('App\Models\SponsoredPost');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new AdminResetPassword($token));
    }
}
