<?php

namespace App;

use App\Mail\NewUserWelcomeMail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'email', 'password',
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


    protected static function boot ()
    {
        parent::boot();
        // This event gets fired whenever a new user has been created
        static::created(

            function ( $user ) {
                // create profile
                $user->profile()->create( [
                    'title' => $user->username
                ] );

                // send e-mail
                Mail::to( $user->email )->send( new NewUserWelcomeMail() );
            }
        );
    }

    public function posts ()
    {
        return $this->hasMany( Posts::class )->orderBy( 'created_at', 'DESC' );
    }

    public function following ()
    {
        return $this->belongsToMany( Profile::class );
    }

    public function profile ()
    {
        return $this->hasOne( Profile::class );
    }
}