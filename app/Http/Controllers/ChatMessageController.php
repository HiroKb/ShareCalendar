<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChatMessageRequest;
use App\Models\SharedCalendar;
use App\Services\ChatMessageService;

class ChatMessageController extends Controller
{
    /**
     * メッセージ一覧
     * @param SharedCalendar $sharedCalendar
     * @param ChatMessageService $chatMessageService
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(SharedCalendar $sharedCalendar, ChatMessageService $chatMessageService)
    {
        $this->authorize('messageIndex', $sharedCalendar);
        return $chatMessageService->index($sharedCalendar);
    }

    /**
     * メッセージ作成
     * @param SharedCalendar $sharedCalendar
     * @param ChatMessageRequest $request
     * @param ChatMessageService $chatMessageService
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(SharedCalendar $sharedCalendar, ChatMessageRequest $request, ChatMessageService $chatMessageService)
    {
        $this->authorize('storeSchedule', $sharedCalendar);
        return $chatMessageService->store($sharedCalendar, $request);
    }
}
