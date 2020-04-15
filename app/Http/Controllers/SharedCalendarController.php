<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSharedCalendarRequest;
use App\Http\Requests\ProcessingApplicationToSharingCalendarRequest;
use App\SharedCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * @param SharedCalendar $sharedCalendar
     * @return SharedCalendar
     */
    public function index(SharedCalendar $sharedCalendar)
    {
        $user_id = Auth::id();
        if (!$sharedCalendar->members()->where('user_id', $user_id)->exists()){
            abort(404);
        }

        if ($sharedCalendar->admin_id !== $user_id){
            unset($sharedCalendar['search_id']);
        }
        return $sharedCalendar;
    }

    /**
     * カレンダ共有メンバー
     * @param SharedCalendar $sharedCalendar
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function membersList(SharedCalendar $sharedCalendar)
    {
        if (!$sharedCalendar->members()->where('user_id', Auth::id())->exists()){
            abort(404);
        }

        return $sharedCalendar
            ->members()
            ->withPivot('created_at AS joined_at')
            ->orderBy('joined_at', 'asc')
            ->get();
    }

    /**
     * 共有申請者リスト
     * @param SharedCalendar $sharedCalendar
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function applicationsList(SharedCalendar $sharedCalendar)
    {
        // カレンダー管理者以外のアクセスの場合
        if ($sharedCalendar->admin_id !== Auth::id()) {
            abort(404);
        }
        return $sharedCalendar
            ->applicants()
            ->withPivot('created_at AS application_at')
            ->orderBy('application_at', 'asc')
            ->get();

    }

    public function allowApplication(SharedCalendar $sharedCalendar, Request $request)
    {
//        カレンダー管理者以外のアクセスの場合
        if ($sharedCalendar->admin_id !== Auth::id()) {
            abort(404);
        }

//        送られてきたIDが全て共有申請済みか確認
        foreach ($request->id_list as $applicantId) {
            if (!$sharedCalendar->applicants()->where('user_id', $applicantId)->exists()){
                abort(404);
                break;
            }
        }

        return DB::transaction(function () use($sharedCalendar, $request){

            $sharedCalendar->members()->sync($request->id_list, false);
            $sharedCalendar->applicants()->detach($request->id_list);
            return response([], 201);
        });
    }

    public function rejectApplication(SharedCalendar $sharedCalendar, Request $request)
    {
//        カレンダー管理者以外のアクセスの場合
        if ($sharedCalendar->admin_id !== Auth::id()) {
            abort(404);
        }
//        送られてきたIDが全て共有申請済みか確認
        foreach ($request->id_list as $applicantId) {
            if (!$sharedCalendar->applicants()->where('user_id', $applicantId)->exists()){
                abort(404);
                break;
            }
        }
        $sharedCalendar->applicants()->detach($request->id_list);
        return response([], 200);
    }

    /**
     * 共有カレンダー検索
     * @param $searchId
     * @return array
     */
    public function search($searchId)
    {
        $userId = Auth::id();
        $sharedCalendar = SharedCalendar::where('search_id', $searchId)->first();
        if (!$sharedCalendar){
            return [
                'status' => 'NotFound',
                'search_id' => '',
                'admin_name' => ''
            ];
        }
        if ($sharedCalendar->members()->where('user_id', $userId)->exists()){
            return [
                'status' => 'Shared',
                'search_id' => '',
                'admin_name' => ''
            ];
        }
        if ($sharedCalendar->applicants()->where('user_id', $userId)->exists()) {
            return [
                'status' => 'Applied',
                'search_id' => '',
                'admin_name' => ''
            ];
        }
        return [
            'status' => 'NotShared',
            'search_id' => $sharedCalendar->search_id,
            'admin_name' => $sharedCalendar->admin->name
        ];
    }

    // カレンダー共有申請
    public function application($searchId)
    {
        $userId = Auth::id();
        $sharedCalendar = SharedCalendar::where('search_id', $searchId)->first();
//        IDが間違っているかすでに共有済み、申請済みの場合
        if (!$sharedCalendar ||
            $sharedCalendar->members()->where('user_id', $userId)->exists() ||
            $sharedCalendar->applicants()->where('user_id', $userId)->exists()){
            abort(404);
        }
        $sharedCalendar->applicants()->attach([$userId]);

        return response([], 201);

    }
}
