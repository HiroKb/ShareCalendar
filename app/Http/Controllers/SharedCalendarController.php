<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSharedCalendarRequest;
use App\SharedCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SharedCalendarController extends Controller
{
    /**
     * 参加共有カレンダー一覧
     * @return mixed
     */
    public function list()
    {
        return Auth::user()->sharedCalendars()
                           ->withPivot('created_at AS joined_at')
                           ->orderBy('joined_at', 'asc')
                           ->get();
    }

    /**
     * 共有カレンダー作成
     * @param CreateSharedCalendarRequest $request
     * @return mixed
     */
    public function create(CreateSharedCalendarRequest $request)
    {
        return DB::transaction(function () use($request){
            $calendar = new SharedCalendar();
            $calendar->calendar_name = $request->calendar_name;
            $calendar->admin_id = Auth::id();
            $calendar->save();

            $calendar->members()->attach([Auth::id()]);

            return $calendar;
        });
    }
}
