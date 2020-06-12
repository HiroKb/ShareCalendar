<?php
namespace App\Services;

use App\Http\Requests\ImageRequest;
use App\Models\SharedCalendar;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class SharedCalendarService
{
    /**
     * カレンダー画像更新
     * @param SharedCalendar $sharedCalendar
     * @param ImageRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateImage(SharedCalendar $sharedCalendar, ImageRequest $request)
    {
        $file = $request->image;
        $tmpFileName = Str::random(20) . $file->getClientOriginalName();
        $tmpPath = storage_path('app/tmp/') . $tmpFileName;

//        中心を基準に200×200pxに切り抜きローカルtmpに保存
        $image = Image::make($file);
        $shortSide = $image->height() > $image->width() ? $image->width() : $image->height();
        $image->resizeCanvas($shortSide, $shortSide)->resize(200,200);
        $image->save($tmpPath);


        DB::beginTransaction();
        $beforeUpdatePath = $sharedCalendar->image_path;
        try {
//          s3にアップロード・ローカルtmp内のファイル削除
            $uploadPath = Storage::disk('s3')->putFile('image', new File($tmpPath), 'public');
            Storage::disk('local')->delete('tmp/' . $tmpFileName);

//            DB内のパスを更新・以前の画像があればs3から削除
            $updatedCalendar = $sharedCalendar->updateImagePath($uploadPath);
            if ($beforeUpdatePath && Storage::disk('s3')->exists($beforeUpdatePath)){
                Storage::disk('s3')->delete($beforeUpdatePath);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Storage::disk('s3')->delete($uploadPath);
            return response([], 500);
        }
        return response($updatedCalendar, 201);
    }

    public function deleteCalendar(SharedCalendar $sharedCalendar)
    {
        $image_path = $sharedCalendar->image_path;
        if ($image_path === null){
            $sharedCalendar->delete();
            return [];
        }

        DB::beginTransaction();
        try {
            $sharedCalendar->delete();
            Storage::disk('s3')->delete($image_path);
            DB::commit();
            return [];
        } catch (\Exception $e) {
            DB::rollBack();
            return response([], 500);
        }
    }

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
        DB::transaction(function () use($sharedCalendar, $idList){
            $sharedCalendar->members()->sync($idList, false);
            $sharedCalendar->applicants()->detach($idList);
        });
        return response([], 201);
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
        if ($sharedCalendar === null){
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
