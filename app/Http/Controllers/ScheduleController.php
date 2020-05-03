<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function index($from, $until)
    {
        $schedules = Auth::user()->schedules()
                                 ->whereBetween('date', [$from, $until])
                                 ->orderBy('time', 'asc')
                                 ->get()
                                 ->groupBy('date');

        return response($schedules);
    }

    /**
     * スケジュール登録
     * @param CreateScheduleRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function create(CreateScheduleRequest $request)
    {
        $schedule = new Schedule();

        $schedule->date = $request->date;
        $schedule->time = $request->time;
        $schedule->title = $request->title;
        $schedule->description = $request->description;


        Auth::user()->schedules()->save($schedule);

        return response($schedule, 201);
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        if ($schedule->user_id !== Auth::id()){
            abort(response()->json([],404));
        }

        $schedule->time = $request->time;
        $schedule->title = $request->title;
        $schedule->description = $request->description;

        $schedule->save();

        return $schedule;
    }

    /**
     * スケジュール削除
     * @param Schedule $schedule
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Schedule $schedule)
    {

        if ($schedule->user_id !== Auth::id()){
            abort(response()->json([],404));
        }

        $schedule->delete();

        return response($schedule, 200);
    }
}
