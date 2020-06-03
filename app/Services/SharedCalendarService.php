<?php
namespace App\Services;

use App\Models\SharedCalendar;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SharedCalendarService
{
    /**
     * 共有メンバー取得
     * @param SharedCalendar $sharedCalendar
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getMembersList(SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar
            ->members()
            ->withPivot('created_at AS joined_at')
            ->orderBy('joined_at', 'asc')
            ->get();
    }

    /**
     * 共有申請者取得
     * @param SharedCalendar $sharedCalendar
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getApplicationsList(SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar
            ->applicants()
            ->withPivot('created_at AS application_at')
            ->orderBy('application_at', 'asc')
            ->get();
    }

    /**
     * 共有申請の許可(メンバーへの追加・申請者から削除)
     * @param SharedCalendar $sharedCalendar
     * @param array $idList
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|mixed
     */
    public function allowApplication(SharedCalendar $sharedCalendar, Array $idList)
    {
//        送られてきたIDが全て共有申請済みか確認
        $existApplicantId = $sharedCalendar->applicants()->get()->pluck('id')->all();
        foreach ($idList as $applicantId) {
            if (!in_array($applicantId, $existApplicantId)) {
                return response([], 404);
            }
        }
        return DB::transaction(function () use($sharedCalendar, $idList){
            $sharedCalendar->members()->sync($idList, false);
            $sharedCalendar->applicants()->detach($idList);
            return response([], 201);
        });
    }

    /**
     * メンバーから削除(共有解除)
     * @param SharedCalendar $sharedCalendar
     * @param $memberId
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function removeMember(SharedCalendar $sharedCalendar, $memberId)
    {
        $userId = Auth::id();
        if (($userId !== $sharedCalendar->admin_id && isset($memberId)) ||
            ($userId === $sharedCalendar->admin_id && !isset($memberId))){
            return response([], 404);
        }
        $memberId = $memberId ?? $userId;
        if ($memberId === $sharedCalendar->admin_id){
            return response([], 404);
        }
        $sharedCalendar->members()->detach($memberId);
        return [];
    }

    /**
     * カレンダー検索
     * @param $searchId
     * @return array
     */
    public function search($searchId)
    {
        $userId = Auth::id();
        $sharedCalendar = SharedCalendar::where('search_id', $searchId)->first();
        if (!$sharedCalendar){
            return [
                'isApplicable' => false,
                'message' => '共有カレンダーが見つかりません。'
            ];
        }
        if ($sharedCalendar->members()->where('user_id', $userId)->exists()){
            return [
                'isApplicable' => false,
                'message' => '共有済みのカレンダーです。'
            ];
        }
        if ($sharedCalendar->applicants()->where('user_id', $userId)->exists()) {
            return [
                'isApplicable' => false,
                'message' => '共有申請済みのカレンダーです。'
            ];
        }
        return [
            'isApplicable' => true,
            'search_id' => $sharedCalendar->search_id,
            'admin_name' => $sharedCalendar->admin->name,
            'admin_image' => $sharedCalendar->admin->image_url
        ];
    }

    /**
     * 共有申請
     * @param $searchId
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function application($searchId)
    {
        $userId = Auth::id();
        $sharedCalendar = SharedCalendar::where('search_id', $searchId)->first();
        if (!$sharedCalendar ||
            $sharedCalendar->members()->where('user_id', $userId)->exists() ||
            $sharedCalendar->applicants()->where('user_id', $userId)->exists()){
            return response([], 404);
        }
        $sharedCalendar->applicants()->attach([$userId]);

        return response([], 201);
    }
}
