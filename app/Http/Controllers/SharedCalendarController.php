<?php

namespace App\Http\Controllers;

use App\Http\Requests\IdListRequest;
use App\Http\Requests\ImageRequest;
use App\Http\Requests\SharedCalendarNameRequest;
use App\Models\SharedCalendar;
use App\Services\SharedCalendarService;
use Illuminate\Support\Facades\DB;

class SharedCalendarController extends Controller
{
    /**
     * カレンダーのデータを取得
     * @param SharedCalendar $sharedCalendar
     * @return SharedCalendar|mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(SharedCalendar $sharedCalendar)
    {
        $this->authorize('show', $sharedCalendar);
        return $sharedCalendar->getCalendarData();
    }

    /**
     * カレンダー作成
     * @param SharedCalendarNameRequest $request
     * @return mixed
     */
    public function store(SharedCalendarNameRequest $request)
    {
        return SharedCalendar::storeSharedCalendar($request);
    }

    /**
     * カレンダー名変更
     * @param SharedCalendar $sharedCalendar
     * @param SharedCalendarNameRequest $request
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateName(SharedCalendar $sharedCalendar, SharedCalendarNameRequest $request)
    {
        $this->authorize('updateName', $sharedCalendar);
        return $sharedCalendar->updateName($request);
    }

    /**
     * カレンダー画像更新
     * @param SharedCalendar $sharedCalendar
     * @param ImageRequest $request
     * @param SharedCalendarService $sharedCalendarService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateImage(SharedCalendar $sharedCalendar, ImageRequest $request, SharedCalendarService $sharedCalendarService)
    {
        $this->authorize('updateImage', $sharedCalendar);
        return $sharedCalendarService->updateImage($sharedCalendar, $request);
    }

    /**
     * カレンダー検索ID変更
     * @param SharedCalendar $sharedCalendar
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateSearchId(SharedCalendar $sharedCalendar)
    {
        $this->authorize('updateSearchId', $sharedCalendar);
        return $sharedCalendar->updateSearchId();
    }

    /**
     * カレンダー削除
     * @param SharedCalendar $sharedCalendar
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(SharedCalendar $sharedCalendar, SharedCalendarService $sharedCalendarService)
    {
        $this->authorize('updateSearchId', $sharedCalendar);
        return $sharedCalendarService->deleteCalendar($sharedCalendar);
    }

    /**
     * 共有メンバーリスト
     * @param SharedCalendar $sharedCalendar
     * @param SharedCalendarService $sharedCalendarService
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function membersList(SharedCalendar $sharedCalendar, SharedCalendarService $sharedCalendarService)
    {
        $this->authorize('membersList', $sharedCalendar);
        return $sharedCalendarService->getMembersList($sharedCalendar);
    }

    /**
     * 共有申請者リスト
     * @param SharedCalendar $sharedCalendar
     * @param SharedCalendarService $sharedCalendarService
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function applicationsList(SharedCalendar $sharedCalendar, SharedCalendarService $sharedCalendarService)
    {
        $this->authorize('applicationsList', $sharedCalendar);
        return $sharedCalendarService->getApplicationsList($sharedCalendar);
    }

    /**
     * 共有申請許可
     * @param SharedCalendar $sharedCalendar
     * @param IdListRequest $request
     * @param SharedCalendarService $sharedCalendarService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function allowApplication(SharedCalendar $sharedCalendar, IdListRequest $request, SharedCalendarService $sharedCalendarService)
    {
        $this->authorize('allowApplication', $sharedCalendar);
        return $sharedCalendarService->allowApplication($sharedCalendar, $request->id_list);
    }

    /**
     * 共有申請拒否
     * @param SharedCalendar $sharedCalendar
     * @param IdListRequest $request
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function rejectApplication(SharedCalendar $sharedCalendar, IdListRequest $request)
    {
        $this->authorize('rejectApplication', $sharedCalendar);
        $sharedCalendar->applicants()->detach($request->id_list);
        return [];
    }

    /**
     * メンバーから削除(共有解除)
     * @param SharedCalendar $sharedCalendar
     * @param null $memberId
     * @param SharedCalendarService $sharedCalendarService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function removeMember(SharedCalendar $sharedCalendar, $memberId = null, SharedCalendarService $sharedCalendarService)
    {
        $this->authorize('removeMember', $sharedCalendar);
        return $sharedCalendarService->removeMember($sharedCalendar, $memberId);
    }


    /**
     * カレンダー検索
     * @param $searchId
     * @param SharedCalendarService $sharedCalendarService
     * @return array
     */
    public function search($searchId, SharedCalendarService $sharedCalendarService)
    {
        return $sharedCalendarService->search($searchId);
    }

    /**
     * カレンダー共有申請
     * @param $searchId
     * @param SharedCalendarService $sharedCalendarService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function application($searchId, SharedCalendarService $sharedCalendarService)
    {
        return $sharedCalendarService->application($searchId);
    }
}
