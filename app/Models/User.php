<?php

namespace App\Models;

use App\Notifications\ResetPasswordJp;
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
        'email', 'email_verified_at', 'password', 'remember_token', 'created_at', 'updated_at'
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
     * 本人しか取得できないユーザーデータのアクセサ
     * @return array
     */
    public function getPrivateDataAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
        ];
    }

    /**
     * パスワード再設定メールのオーバーライド
     *
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordJp($token));
    }


    public function schedules()
    {
        return $this->hasMany('App\Models\Schedule');
    }

    public function sharedCalendars()
    {
       return $this->belongsToMany('App\Models\SharedCalendar', 'shared_calendar_user_members', 'user_id', 'calendar_id')
                    ->withTimestamps();
    }

    public function applicationCalendars()
    {
        return $this->belongsToMany('App\Models\SharedCalendar', 'shared_calendar_user_applications', 'user_id', 'calendar_id')
            ->withTimestamps();
    }



}
