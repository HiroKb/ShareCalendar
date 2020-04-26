<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleRequest;
use App\SharedCalendar;
use App\SharedSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SharedScheduleController extends Controller
{
    public function list(SharedCalendar $sharedCalendar, $from, $until)
    {
        if (!$sharedCalendar->members()->where('user_id', Auth::id())->exists()){
            abort(404);
        }

        return $sharedCalendar->schedules()
                              ->whereBetween('date', [$from, $until])
                              ->orderBy('time', 'asc')
                              ->get()
                              ->groupBy('date');
    }
    /**
     * スケジュール作成
     * @param SharedCalendar $sharedCalendar
     * @param CreateScheduleRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function create(SharedCalendar $sharedCalendar, CreateScheduleRequest $request)
    {
        if (!$sharedCalendar->members()->where('user_id', Auth::id())->exists()){
            abort(404);
        }
        $schedule = new SharedSchedule();

        $schedule->date = $request->date;
        $schedule->time = $request->time;
        $schedule->title = $request->title;
        $schedule->description = $request->description;

        $sharedCalendar->schedules()->save($schedule);

        return response($schedule, 201);
    }

    public function destroy(SharedCalendar $sharedCalendar, SharedSchedule $sharedSchedule)
    {
        if (!$sharedCalendar->members()->where('user_id', Auth::id())->exists()){
            abort(404);
        }
        $sharedSchedule->delete();

        return response([], 200);
    }
}
