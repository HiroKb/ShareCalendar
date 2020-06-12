<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class ChatMessage extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['id'] = Uuid::uuid4()->toString();
    }

    protected $hidden = [
        'id', 'calendar_id', 'updated_at'
    ];

    public function calendar()
    {
        return $this->belongsTo('App\Models\SharedCalendar', 'calendar_id', 'id');
    }

    public function postedUser()
    {
        return $this->belongsTo('App\Models\User', 'posted_user_id', 'id');
    }
}
