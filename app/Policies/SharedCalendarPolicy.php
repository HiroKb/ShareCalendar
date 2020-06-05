<?php

namespace App\Policies;

use App\Models\SharedCalendar;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SharedCalendarPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    // SharedCalendarControllerに関わるもの
    public function show(User $user, SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->members()->where('user_id', $user->id)->exists();
    }

    public function updateName(User $user, SharedCalendar $sharedCalendar)
    {
        return $user->id === $sharedCalendar->admin_id;
    }

    public function updateImage(User $user, SharedCalendar $sharedCalendar)
    {
        return $user->id === $sharedCalendar->admin_id;
    }

    public function updateSearchId(User $user, SharedCalendar $sharedCalendar)
    {
        return $user->id === $sharedCalendar->admin_id;
    }

    public function destroy(User $user, SharedCalendar $sharedCalendar)
    {
        return $user->id === $sharedCalendar->admin_id;
    }

    public function membersList(User $user, SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->members()->where('user_id', $user->id)->exists();
    }

    public function applicationsList(User $user, SharedCalendar $sharedCalendar)
    {
        return $user->id === $sharedCalendar->admin_id;
    }

    public function allowApplication(User $user, SharedCalendar $sharedCalendar)
    {
        return $user->id === $sharedCalendar->admin_id;
    }

    public function rejectApplication(User $user, SharedCalendar $sharedCalendar)
    {
        return $user->id === $sharedCalendar->admin_id;
    }

    public function removeMember(User $user, SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->members()->where('user_id', $user->id)->exists();
    }

    // SharedScheduleControllerに関わるもの
    public function scheduleIndex(User $user, SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->members()->where('user_id', $user->id)->exists();
    }
    public function storeSchedule(User $user, SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->members()->where('user_id', $user->id)->exists();
    }
    public function destroySchedule(User $user, SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->members()->where('user_id', $user->id)->exists();
    }
    public function updateSchedule(User $user, SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->members()->where('user_id', $user->id)->exists();
    }

    // ChatMessageControllerに関わるもの
    public function messageIndex(User $user, SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->members()->where('user_id', $user->id)->exists();
    }
    public function storeMessage(User $user, SharedCalendar $sharedCalendar)
    {
        return $sharedCalendar->members()->where('user_id', $user->id)->exists();
    }
}
