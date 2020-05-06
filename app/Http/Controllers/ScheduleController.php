<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Schedule;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScheduleController extends Controller
{
    public function list($from, $until)
    {
        $personalSchedules = Auth::user()->schedules()
            ->whereBetween('date', [$from, $until])
            ->get()
            ->toArray();
        $sharedSchedules = [];

        $sharedDataCollection = User::with([
            'sharedCalendars.schedules' => function($query) use($from, $until){
            $query->whereBetween('date', [$from, $until]);
            }])->find(Auth::id());
        $sharedCalendars = $sharedDataCollection->toArray()['shared_calendars'];

        foreach ($sharedCalendars as $sharedCalendar){
            foreach ($sharedCalendar['schedules'] as $sharedSchedule){
                $sharedSchedule['calendar_name'] = $sharedCalendar['calendar_name'];
                $sharedSchedules[] = $sharedSchedule;
            }
        }

        $schedulesCollection = collect(array_merge($personalSchedules, $sharedSchedules));

        $sortedSchedules = $schedulesCollection
                            ->sortBy('time' )
                            ->groupBy('date');

        return response($sortedSchedules);
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
