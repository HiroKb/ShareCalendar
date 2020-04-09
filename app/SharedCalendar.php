<?php

namespace App;

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
        return $this->belongsTo('App\User', 'admin_id', 'id');
    }

    public function members()
    {
        return $this->belongsToMany('App\User','shared_calendar_user', 'calendar_id', 'user_id')
                    ->withTimestamps();
    }

    public function applicants()
    {
        return $this->belongsToMany('App\User','sharedcalendar_user_applicants', 'calendar_id', 'user_id')
            ->withTimestamps();
    }
}
