<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UserService
{
    /**
     * ユーザー画像更新
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function updateImage($request)
    {
        $file = $request->image;
        $tmpFileName = Str::random(20) . $file->getClientOriginalName();
        $tmpPath = storage_path('app/tmp/') . $tmpFileName;

//        中心を基準に200×200pxに切り抜きローカルtmpに保存
        $image = Image::make($file);
        $shortSide = $image->height() > $image->width() ? $image->width() : $image->height();
        $image->resizeCanvas($shortSide, $shortSide)->resize(200,200);
        $image->save($tmpPath);

//        s3にアップロード・ローカルtmp内のファイル削除
        $uploadPath = Storage::disk('s3')->putFile('image', new File($tmpPath), 'public');
        Storage::disk('local')->delete('tmp/' . $tmpFileName);

        DB::beginTransaction();
//        DB内のパスを更新・以前の画像があればs3から削除
        $user = Auth::user();
        $beforeUpdatePath = $user->image_path;
        try {
            $updatedUser = $user->updateImagePath($uploadPath);
            if ($beforeUpdatePath && Storage::disk('s3')->exists($beforeUpdatePath)){
                Storage::disk('s3')->delete($beforeUpdatePath);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Storage::disk('s3')->delete($uploadPath);
            return response([], 500);
        }
        return response($updatedUser, 201);
    }
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
