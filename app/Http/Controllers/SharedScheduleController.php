<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\SharedCalendar;
use App\Models\SharedSchedule;
use App\Services\SharedScheduleService;

class SharedScheduleController extends Controller
{
    /**
     * $sharedCalendarに属する$fromから$untilまでの期間の共有スケジュールを取得
     * @param SharedCalendar $sharedCalendar
     * @param $from
     * @param $until
     * @param SharedScheduleService $sharedScheduleService
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(SharedCalendar $sharedCalendar, $from, $until, SharedScheduleService $sharedScheduleService)
    {
        $this->authorize('scheduleIndex', $sharedCalendar);
        return $sharedScheduleService->index($sharedCalendar, $from, $until);
    }

    /**
     * 共有スケジュール作成
     * @param SharedCalendar $sharedCalendar
     * @param CreateScheduleRequest $request
     * @param SharedScheduleService $sharedScheduleService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(SharedCalendar $sharedCalendar, CreateScheduleRequest $request, SharedScheduleService $sharedScheduleService)
    {
        $this->authorize('storeSchedule', $sharedCalendar);
        return $sharedScheduleService->store($sharedCalendar, $request->all());
    }

    /**
     * 共有スケジュール削除
     * @param SharedCalendar $sharedCalendar
     * @param SharedSchedule $sharedSchedule
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(SharedCalendar $sharedCalendar, SharedSchedule $sharedSchedule)
    {
        $this->authorize('destroySchedule', $sharedCalendar);
        $sharedSchedule->delete();
        return [];
    }

    /**
     * 共有スケジュール更新
     * @param SharedCalendar $sharedCalendar
     * @param SharedSchedule $sharedSchedule
     * @param UpdateScheduleRequest $request
     * @param SharedScheduleService $sharedScheduleService
     * @return SharedSchedule
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(SharedCalendar $sharedCalendar, SharedSchedule $sharedSchedule, UpdateScheduleRequest $request, SharedScheduleService $sharedScheduleService)
    {
        $this->authorize('destroySchedule', $sharedCalendar);
        return $sharedScheduleService->update($sharedSchedule, $request);
    }
}
