<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SharedCalendar extends Model
{
    protected $table = 'shared_calendars';
    public function members()
    {
        return $this->belongsToMany('App\User','shared_calendar_user', 'calendar_id', 'user_id');
    }
}
