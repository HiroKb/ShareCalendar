<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;

class SharedCalendar extends Model
{
    protected $table = 'shared_calendars';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'calendar_name'
    ];

    protected $appends = [
        'image_url'
    ];

    protected $hidden = [
        'search_id', 'created_at', 'updated_at'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->attributes['id'] = Uuid::uuid4()->toString();

        $this->attributes['search_id'] = Uuid::uuid4()->toString();
    }

    public function getImageUrlAttribute()
    {
        if ($this->image_path){
            return Storage::disk('s3')->url($this->image_path);
        }
        return null;
    }

    /**
     * カレンダー管理者のみが取得できるデータ
     * @return array
     */
    public function getDataForAdminAttribute()
    {
        return [
            'id' => $this->id,
            'search_id' => $this->search_id,
            'admin_id' => $this->admin_id,
            'calendar_name' => $this->calendar_name,
            'image_url' => $this->image_url
        ];
    }

    /**
     * リレーション(カレンダー管理者)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo('App\Models\User', 'admin_id', 'id');
    }

    /**
     * リレーション(カレンダー共有メンバー)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members()
    {
        return $this->belongsToMany('App\Models\User','shared_calendar_user_members', 'calendar_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * リレーション(共有申請者)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function applicants()
    {
        return $this->belongsToMany('App\Models\User','shared_calendar_user_applications', 'calendar_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * リレーション(共有スケジュール)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function schedules()
    {
        return $this->hasMany('App\Models\SharedSchedule', 'calendar_id', 'id');
    }

    /**
     * リレーション(チャットメッセージ)
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatMessages()
    {
        return $this->hasMany('App\Models\ChatMessage', 'calendar_id', 'id');
    }

    /**
     * カレンダーデータ返却
     * ユーザーが管理者であれば管理者用データ、それ以外はsearch_idを除外したデータを返却
     * @return $this|mixed
     */
    public function getCalendarData()
    {
        if ($this->admin_id === Auth::id()){
            return $this->data_for_admin;
        }
        return $this;
    }

    /**
     * カレンダー作成
     * member中間テーブルにデータを追加
     * @param $request
     * @return mixed
     */
    public static function storeSharedCalendar($request)
    {
        $userId = Auth::id();
        $calendar = new static();
        $calendar->calendar_name = $request->calendar_name;
        $calendar->admin_id = $userId;

        return DB::transaction(function () use($calendar, $userId){
            $calendar->save();
            $calendar->members()->attach([$userId]);
            return $calendar->data_for_admin;
        });
    }

    /**
     * カレンダー名変更
     * @param $request
     * @return mixed
     */
    public function updateName($request)
    {
        $this->calendar_name = $request->calendar_name;
        $this->save();
        return $this->data_for_admin;
    }

    /**
     * イメージパス更新
     * @param $path
     * @return mixed
     */
    public function updateImagePath($path)
    {
        $this->image_path = $path;
        $this->save();
        return $this->data_for_admin;
    }

    /**
     * 検索ID変更(Uuid)
     * @return mixed
     * @throws \Exception
     */
    public function updateSearchId()
    {
        $this->search_id = Uuid::uuid4()->toString();
        $this->save();
        return $this->data_for_admin;
    }
}
