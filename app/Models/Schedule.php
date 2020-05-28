<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Schedule extends Model
{
    protected $table = 'schedules';
    protected $keyType = 'string';
    public $incrementing = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['id'] = Uuid::uuid4()->toString();
    }

    protected $guarded = [
        'id', 'created_at', 'updated_at'
    ];

    protected $attributes = [
        'time' => null,
        'description' => null
    ];


    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

}
