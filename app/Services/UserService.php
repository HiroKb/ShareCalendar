<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * $fromから$untilまでの個人スケジュールと共有スケジュールのリスト
     * @param $from
     * @param $until
     * @return \Illuminate\Support\Collection
     */
    public function getSchedulesList($from, $until)
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

        return $sortedSchedules;
    }

    /**
     * 参加している共有カレンダーのリスト
     * @return mixed
     */
    public function getSharedCalendarsList()
    {
        return Auth::user()->sharedCalendars()
            ->withPivot('created_at AS joined_at')
            ->orderBy('joined_at', 'asc')
            ->get();
    }
}
