<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use \App\Notifications\EmailUpdate as EmailUpdateNotification;

class EmailUpdate extends Model
{
    use Notifiable;
    protected $fillable = [
        'user_id', 'new_email', 'token'
    ];

    /**
     * 送信先アドレスを取得するカラムの変更
     * @return mixed
     */
    public function routeNotificationForMail($notification)
    {
        return $this->new_email;
    }

    /**
     * 通知の送信
     * @param $token
     */
    public function sendEmailUpdateNotification($token)
    {
        $this->notify(new EmailUpdateNotification($token));
    }
}
