<?php

namespace App\Models;

use App\Notifications\ResetPasswordJp;
use App\Notifications\VerifyEmailJp;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class User extends Authenticatable implements MustVerifyEmail
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

    protected $appends = [
        'image_url'
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

    public function getImageUrlAttribute()
    {
         if ($this->image_path){
             return Storage::disk('s3')->url($this->image_path);
         }
         return null;
    }

    /**
     * 本人しか取得できないユーザーデータのアクセサ
     * @return array
     */
    public function getPrivateDataAttribute()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image_url' => $this->image_url,
            'email' => $this->email,
            'passwordExists' => !!$this->password
        ];
    }

    /**
     * メールアドレス確認用メールのオーバーライド
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailJp);
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

    /**
     * リレーション(個人スケジュール)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules()
    {
        return $this->hasMany('App\Models\Schedule');
    }

    /**
     * リレーション(共有しているカレンダー)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sharedCalendars()
    {
       return $this->belongsToMany('App\Models\SharedCalendar', 'shared_calendar_user_members', 'user_id', 'calendar_id')
                    ->withTimestamps();
    }

    /**
     * リレーション(共有を申請しているカレンダー)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function applicationCalendars()
    {
        return $this->belongsToMany('App\Models\SharedCalendar', 'shared_calendar_user_applications', 'user_id', 'calendar_id')
            ->withTimestamps();
    }

    /**
     * ユーザー名更新処理
     * @param $request
     * @return mixed
     */
    public function updateName($request)
    {
        $this->name = $request->name;
        $this->save();
        return $this->private_data;
    }

    /**
     * イメージパスの更新
     * @param $path
     * @return mixed
     */
    public function updateImagePath($path)
    {
        $this->image_path = $path;
        $this->save();
        return $this->private_data;
    }

    /**
     * メールアドレス変更処理
     * @param $request
     * @return mixed
     */
    public function updateEmail($request)
    {
        $this->email = $request->email;
        $this->save();
        return $this->private_data;
    }

    /**
     * パスワード変更処理
     * @param $request
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updatePassword($request)
    {
        if(!$this->password) {
            return response([], 404);
        }
        $this->password = Hash::make($request->new_password);
        $this->save();
        return [];
    }

    /**
     * パスワード新規登録
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function registrationPassword($request)
    {
        if(!!$this->password) {
            return response([], 404);
        }
        $this->password = Hash::make($request->password);
        $this->save();
        return response([], 201);
    }

}
