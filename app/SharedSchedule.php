<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class SharedSchedule extends Model
{
    protected $table = 'shared_schedules';
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
}
