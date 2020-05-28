<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Http\Requests\ChatMessageRequest;
use App\Models\SharedCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatMessageController extends Controller
{
    public function list(SharedCalendar $sharedCalendar)
    {
        if (!$sharedCalendar->members()->where('user_id', Auth::id())->exists()){
            abort(404);
        }

        return $sharedCalendar->chatMessages()
                               ->with('postedUser')
                               ->orderBy('created_at', 'asc')
                               ->get();
    }
    /**
     * チャットメッセージ作成
     * @param SharedCalendar $sharedCalendar
     * @param ChatMessageRequest $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function create(SharedCalendar $sharedCalendar, ChatMessageRequest $request)
    {
        $userId = Auth::id();
        if (!$sharedCalendar->members()->where('user_id', $userId)->exists()){
            abort(404);
        }

        $message = new ChatMessage();
        $message->posted_user_id = $userId;
        $message->message = $request->message;

        $sharedCalendar->chatMessages()->save($message);

        return response($message, 201);
    }
}
