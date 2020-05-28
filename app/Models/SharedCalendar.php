<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class SharedCalendar extends Model
{
    protected $table = 'shared_calendars';
    protected $keyType = 'string';
    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['id'] = Uuid::uuid4()->toString();

        $this->attributes['search_id'] = Uuid::uuid4()->toString();
    }

    public function admin()
    {
        return $this->belongsTo('App\Models\User', 'admin_id', 'id');
    }

    public function members()
    {
        return $this->belongsToMany('App\Models\User','shared_calendar_user_members', 'calendar_id', 'user_id')
                    ->withTimestamps();
    }

    public function applicants()
    {
        return $this->belongsToMany('App\Models\User','shared_calendar_user_applications', 'calendar_id', 'user_id')
            ->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany('App\Models\SharedSchedule', 'calendar_id', 'id');
    }

    public function chatMessages()
    {
        return $this->hasMany('App\Models\ChatMessage', 'calendar_id', 'id');
    }
}