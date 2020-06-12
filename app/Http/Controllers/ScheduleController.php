<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Schedule;

class ScheduleController extends Controller
{

    /**
     * スケジュール登録
     * @param CreateScheduleRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(CreateScheduleRequest $request)
    {
        return Schedule::storeSchedule($request->all());
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
        return [];
    }
}
