<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
    public function store(CreateScheduleRequest $request)
    {
        return response(Schedule::storeSchedule($request->all()), 201);
    }

    /**
     * スケジュール更新
     * @param UpdateScheduleRequest $request
     * @param Schedule $schedule
     * @return Schedule
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        $this->authorize('update', $schedule);
        return $schedule->updateSchedule($request);
    }

    /**
     * スケジュール削除
     * @param Schedule $schedule
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Schedule $schedule)
    {
        $this->authorize('delete', $schedule);
        $schedule->delete();
        return response([], 200);
    }
}
