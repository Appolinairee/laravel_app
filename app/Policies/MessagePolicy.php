<?php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update message.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function updateMessage(User $user, Message $message)
    {
        return ($user->id === $message->sender_id) || $user->isAdmin();
    }

}
