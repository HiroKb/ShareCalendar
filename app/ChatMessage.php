<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ChatMessage extends Model
{
    protected $table = 'chat_messages';
    protected $keyType = 'string';
    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['id'] = Uuid::uuid4()->toString();
    }

    public function calendar()
    {
        return $this->belongsTo('App\SharedCalendar', 'calendar_id', 'id');
    }

    public function postedUser()
    {
        return $this->belongsTo('App\User', 'posted_user_id', 'id');
    }
}
