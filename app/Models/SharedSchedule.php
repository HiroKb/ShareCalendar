<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class SharedSchedule extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title', 'description', 'date', 'time'
    ];

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['id'] = Uuid::uuid4()->toString();
    }

    /**
     * リレーション(共有カレンダー)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function calendar()
    {
        return $this->belongsTo('App\Models\SharedCalendar', 'calendar_id', 'id');
    }
}
