<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSharedCalendarRequest;
use App\SharedCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class SharedCalendarController extends Controller
{
    public function index(SharedCalendar $calendar)
    {
        $user_id = Auth::id();
        if (!$calendar->members()->where('user_id', $user_id)->exists()){
            abort(404);
        }

        if ($calendar->admin_id !== $user_id){
            unset($calendar['search_id']);
        }
        return $calendar;
    }
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

    public function search($searchId)
    {
        $calendar = SharedCalendar::where('search_id', $searchId)->first();
        return [
                 'search_id' => $calendar->search_id,
                 'admin_name' => $calendar->admin->name
                ];
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
