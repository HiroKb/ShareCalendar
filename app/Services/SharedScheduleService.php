<?php
namespace App\Services;

use App\Models\SharedCalendar;
use App\Models\SharedSchedule;

class SharedScheduleService
{
    /**
     * $sharedCalendarに属する$fromから$untilまでの期間の共有スケジュールを取得
     * @param SharedCalendar $sharedCalendar
     * @param $from
     * @param $until
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index(SharedCalendar $sharedCalendar, $from, $until)
    {
        return $sharedCalendar->schedules()
            ->whereBetween('date', [$from, $until])
            ->orderBy('time', 'asc')
            ->get()
            ->groupBy('date');
    }

    /**
     * 共有スケジュール作成
     * @param SharedCalendar $sharedCalendar
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(SharedCalendar $sharedCalendar, $request)
    {
        $schedule = new SharedSchedule();
        unset($request['_token']);
        $sharedCalendar->schedules()->save($schedule->fill($request));
        return response($schedule, 201);
    }

    /**
     * 共有スケジュール更新
     * @param SharedSchedule $sharedSchedule
     * @param $request
     * @return SharedSchedule
     */
    public function update(SharedSchedule $sharedSchedule, $request)
    {
        $sharedSchedule->time = $request->time;
        $sharedSchedule->title = $request->title;
        $sharedSchedule->description = $request->description;
        $sharedSchedule->save();
        return $sharedSchedule;
    }
}
