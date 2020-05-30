<?php
namespace App\Services;

use App\Models\ChatMessage;
use App\Models\SharedCalendar;
use Illuminate\Support\Facades\Auth;

class ChatMessageService
{
    /**
     * メッセージ一覧取得
     * @param SharedCalendar $sharedCalendar
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index(SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->chatMessages()
            ->with('postedUser')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * メッセージ作成取得
     * @param SharedCalendar $sharedCalendar
     * @param $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function store(SharedCalendar $sharedCalendar, $request)
    {
        $message = new ChatMessage();
        $message->posted_user_id = Auth::id();
        $message->message = $request->message;
        $sharedCalendar->chatMessages()->save($message);
        return response($message, 201);
    }

}
