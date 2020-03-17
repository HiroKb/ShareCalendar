<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';

    protected $guarded = [
        'id', 'created_at', 'updated_at'
    ];

    protected $attributes = [
        'time' => null,
        'description' => null
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
