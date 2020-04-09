<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Ramsey\Uuid\Uuid;

class User extends Authenticatable
{
    use Notifiable;

    protected $keyType = 'string';
    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['id'] = Uuid::uuid4()->toString();
    }

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

    public function schedules()
    {
        return $this->hasMany('App\Schedule');
    }

    public function sharedCalendars()
    {
       return $this->belongsToMany('App\SharedCalendar', 'shared_calendar_user', 'user_id', 'calendar_id')
                    ->withTimestamps();
    }

    public function applicationCalendars()
    {
        return $this->belongsToMany('App\SharedCalendar', 'sharedcalendar_applicants', 'user_id', 'calendar_id')
            ->withTimestamps();
    }
}
