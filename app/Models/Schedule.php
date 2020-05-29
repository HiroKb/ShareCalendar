<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
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
        'id', 'user_id', 'created_at', 'updated_at'
    ];

    protected $hidden =[
        'user_id', 'created_at', 'updated_at'
    ];

    protected $attributes = [
        'time' => null,
        'description' => null
    ];

    /**
     * リレーション(スケジュール所有ユーザー)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * スケジュール作成
     * @param $request
     * @return static
     */
    public static function storeSchedule($request)
    {
        $schedule = new static;
        unset($request['_token']);
        Auth::user()->schedules()->save($schedule->fill($request));
        return $schedule;
    }

    /**
     * スケジュール更新
     * @param $request
     * @return $this
     */
    public function updateSchedule($request)
    {
        $this->time = $request->time;
        $this->title = $request->title;
        $this->description = $request->description;

        $this->save();
        return $this;
    }

}
