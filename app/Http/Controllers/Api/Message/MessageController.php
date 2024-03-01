<?php

namespace App\Http\Controllers\Api\Message;

use App\Http\Controllers\Controller;
use App\Models\Creator;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{

    /**
     * Get messages for a user.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function messagesByUser(User $user, Request $request)
    {
        try {
            $currentUser = auth()->user();

            // Check if the current user is an admin
            if ($currentUser->isAdmin()) {
                return $this->adminMessagesByUser($user, $request);
            }

            return $this->userMessagesByUser($user, $request);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    /**
     * Get messages for a user as an administrator.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    protected function adminMessagesByUser(User $user, Request $request)
    {
        $messages = Message::where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('perPage', 10));

        return $this->responseMessages($messages, 'Admin: Messages for the user.');
    }


    /**
     * Get messages for a user as a regular user.
     *
     * @param  \App\Models\User  $user
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    protected function userMessagesByUser(User $user, Request $request)
    {
        $currentUser = auth()->user();

        $query = $this->getAllMessagesBetweenUsers($user, $currentUser);
        
        $perPage = $request->get('perPage', 10);

        $messages = $query
        ->orderBy('created_at', 'desc')
        ->paginate($request->get('perPage', 10));

        return $this->responseMessages($messages, 'User: Messages partégés avec l\'utilisateur.');
    }



    /**
     * Retrieve messages between the two users.
     *
     * @param  User  $user1 The user with whom messages are exchanged.
     * @param  User  $user2 The user with whom messages are exchanged.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAllMessagesBetweenUsers(User $user1, User $user2)
    {
        $messages =  Message::where(function ($query) use ($user1, $user2) {
            $query->where('sender_id', $user1->id)
                ->where('receiver_id', $user2->id);
        })
            ->orWhere(function ($query) use ($user1, $user2) {
                $query->where('sender_id', $user2->id)
                    ->where('receiver_id', $user1->id);
            });

        return $messages;
    }


    /**
     * Generate JSON response for messages.
     *
     * @param  mixed  $messages
     * @param  string  $message
     * @return \Illuminate\Http\Response
     */
    protected function responseMessages($messages, $message)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $messages->items(),
            'pagination' => [
                'nextUrl' => $messages->nextPageUrl(),
                'prevUrl' => $messages->previousPageUrl(),
                'total' => $messages->total(),
            ],
        ], 201);
    }






    /**
     * Get users with their last messages, excluding the current user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersWithLastMessages()
    {
        return User::where('id', '!=', auth()->id())
            ->whereHas('messages', function ($query) {
                $query->where('sender_id', auth()->id())
                    ->orWhere('receiver_id', auth()->id());
            })
            ->with('lastMessage.sender', 'lastMessage.receiver')
            ->get();
    }











    /**
     * Delete message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function delete(Message $message)
    {
        try {

            $this->authorize('updateMessage', $message);

            // solf delete
            $message->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Message supprimé.'
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
