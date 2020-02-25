<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, Sluggable, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function bankInfo()
    {
        return $this->hasOne('App\Models\BankInfo');
    }

    public function post()
    {
        return $this->hasMany('App\Models\Post');
    }

    public function testimony()
    {
        return $this->hasMany('App\Models\Testimony');
    }

    public function sponsored_comment()
    {
        return $this->hasMany('App\Models\SponsoredComment');
    }

    public function activity()
    {
        return $this->hasMany('App\Models\Activity');
    }

    public function payment_request()
    {
        return $this->hasMany('App\Models\PaymentRequest');
    }

    public function payment()
    {
        return $this->hasMany('App\Models\Payment');
    }

    public function comment()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function referral()
    {
        return $this->hasMany('App\Models\Referral');
    }

    public function history()
    {
        return $this->hasMany('App\Models\LoginHistory');
    }

    public static function countUsers()
    {
        $users     =   self::count();

        return $users;
    }

    public function getUsers()
    {
        $users = self::select('*')->with('bankInfo')->get();

        return $users;
    }
}
