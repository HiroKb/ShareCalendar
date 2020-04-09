<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApplicationToSharingCalendarRequest;
use App\Http\Requests\CreateSharedCalendarRequest;
use App\SharedCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use function Psy\debug;

class SharedCalendarController extends Controller
{
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
     * 共有カレンダーデータ
     * @param SharedCalendar $calendar
     * @return SharedCalendar
     */
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

    public function applicantsList(SharedCalendar $calendar)
    {
        // カレンダー管理者以外のアクセスの場合
        if ($calendar->admin_id !== Auth::id()) {
            abort(404);
        }
        return $calendar->applicants()
            ->withPivot('created_at AS application_at')
            ->orderBy('application_at', 'asc')
            ->get();

    }

    /**
     * 共有カレンダー検索
     * @param $searchId
     * @return array
     */
    public function search($searchId)
    {
        $userId = Auth::id();
        $calendar = SharedCalendar::where('search_id', $searchId)->first();
        if (!$calendar){
            return [
                'status' => 'NotFound',
                'search_id' => '',
                'admin_name' => ''
            ];
        }
        if ($calendar->members()->where('user_id', $userId)->exists()){
            return [
                'status' => 'Shared',
                'search_id' => '',
                'admin_name' => ''
            ];
        }
        if ($calendar->applicants()->where('user_id', $userId)->exists()) {
            return [
                'status' => 'Applied',
                'search_id' => '',
                'admin_name' => ''
            ];
        }
        return [
            'status' => 'NotShared',
            'search_id' => $calendar->search_id,
            'admin_name' => $calendar->admin->name
        ];
    }

    // カレンダー共有申請
    public function application(ApplicationToSharingCalendarRequest $request)
    {
        $userId = Auth::id();
        $calendar = SharedCalendar::where('search_id', $request->search_id)->first();
//        すでに共有済みか申請済みの場合
        if ($calendar->members()->where('user_id', $userId)->exists() ||
            $calendar->applicants()->where('user_id', $userId)->exists()){
            abort(404);
        }
        $calendar->applicants()->attach([$userId]);

        return response([], 201);

    }
}
