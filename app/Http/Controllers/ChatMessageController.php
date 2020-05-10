<?php

namespace App\Http\Controllers;

use App\ChatMessage;
use App\Http\Requests\ChatMessageRequest;
use App\SharedCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatMessageController extends Controller
{
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
