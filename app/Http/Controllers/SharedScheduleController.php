<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleRequest;
use App\SharedCalendar;
use App\SharedSchedule;
use Illuminate\Http\Request;

class SharedScheduleController extends Controller
{
    /**
     * スケジュール作成
     * @param SharedCalendar $sharedCalendar
     * @param CreateScheduleRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function create(SharedCalendar $sharedCalendar, CreateScheduleRequest $request)
    {
        $schedule = new SharedSchedule();

        $schedule->date = $request->date;
        $schedule->time = $request->time;
        $schedule->title = $request->title;
        $schedule->description = $request->description;

        $sharedCalendar->schedules()->save($schedule);

        return response($schedule, 201);
    }
}
