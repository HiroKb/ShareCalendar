<?php
namespace App\Services;

use App\models\EmailUpdate;
use App\Models\User;
use Carbon\Carbon;
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
     * メールアドレス変更確認メール送信・新しいアドレス・ユーザーid・トークンを保存
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function sendUpdateEmailLink($request)
    {
        $new_email = $request->new_email;

        $token = hash_hmac('sha256', Str::random(40).$new_email, config('app.key'));

        $emailUpdate = new EmailUpdate();
        $emailUpdate->user_id = Auth::id();
        $emailUpdate->new_email = $new_email;
        $emailUpdate->token = $token;
        $emailUpdate->save();

        $emailUpdate->sendEmailUpdateNotification($token);
        return response([], 201);
    }

    /**
     * メールアドレス更新処理
     * 確認メール送信から1時間以上経過している場合はリダイレクト
     * @param $token
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateEmail($token)
    {
        $user = Auth::user();
        $emailUpdate = EmailUpdate::where([
            'user_id' => $user->id,
            'token' => $token
        ])->first();

        if ($emailUpdate === null) return redirect('/404');

        if (Carbon::parse($emailUpdate->created_at)->addHour()->isPast()){
            $emailUpdate->delete();
            return redirect('/404');
        }

        $user->email = $emailUpdate->new_email;
        $user->save();
        $emailUpdate->delete();

        return redirect('/personal/account/info');
    }

    /**
     * アカウント削除処理・画像が登録されている場合はs3から画像を削除
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function deleteAccount()
    {
        $user = Auth::user();
        $image_path = $user->image_path;
        if ($image_path === null){
            $user->delete();
            return [];
        }

        DB::beginTransaction();
        try {
            $user->delete();
            Storage::disk('s3')->delete($image_path);
            DB::commit();
            return [];
        } catch (\Exception $e) {
            DB::rollBack();
            return response([], 500);
        }
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
