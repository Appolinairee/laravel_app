<?php

namespace App\Http\Controllers\Api\Message;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

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
        $perPage = $request->get('perPage', 10);

        $messages = Message::withTrashed()->where('sender_id', $user->id)
            ->orWhere('receiver_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);


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
            ->paginate($perPage);

        return $this->responseMessages($messages, 'User: Messages partégés avec l\'utilisateur.');
    }



    /**
     * Retrieve messages between two users.
     *
     * @param  \App\Models\User  $user1 The first user.
     * @param  \App\Models\User  $user2 The second user.
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getAllMessagesBetweenUsers(User $user1, User $user2)
    {
        try {
            $messages = Message::where(function ($query) use ($user1, $user2) {
                $query->where('sender_id', $user1->id)
                    ->where('receiver_id', $user2->id);
            })
                ->orWhere(function ($query) use ($user1, $user2) {
                    $query->where('sender_id', $user2->id)
                        ->where('receiver_id', $user1->id);
                })
                ->get();

            return $messages;
        } catch (Exception $e) {
            return response()->json($e);
        }
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
        $userId = auth()->id();

        $formattedMessages = $messages->map(function ($message) use ($userId) {
            return array_merge(
                $message->toArray(),
                ['is_current_user' => $message->sender_id == $userId]
            );
        });

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $formattedMessages,
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersWithLastMessages()
    {
        try {
            $userId = auth()->id();

            $usersWithMessages = User::where('id', '!=', $userId)
                ->with(['messages' => function ($query) {
                    $query->latest()->first();
                }])
                ->with(['receivedMessages' => function ($query) {
                    $query->latest()->first();
                }])->select('id', 'name')
                ->get();

            $relevantUsersWithMessages = $usersWithMessages->map(function ($user) use ($userId) {
                $latestSentMessage = $user->messages->first();

                $latestReceivedMessage = $user->receivedMessages->first();

                $user->latest_message = $latestSentMessage ? ($latestReceivedMessage ? ($latestSentMessage->created_at > $latestReceivedMessage->created_at ? $latestSentMessage : $latestReceivedMessage) : $latestSentMessage) : $latestReceivedMessage;

                $user->load('creator');
                if ($user->creator) {
                    $user->creator->makeHidden(['creator_balance', 'phone', 'email', 'user_id']);
                }

                if ($user->latest_message) {
                    $carbonDate = Carbon::parse($user->latest_message->updated_at);
                    $user->latest_message->time_ago = $carbonDate->shortRelativeToNowDiffForHumans();
                }

                return $user;
            })->filter(function ($user) {
                return !is_null($user->latest_message);
            });

            $cleanedMessages =  $relevantUsersWithMessages->makeHidden('messages');
            $cleanedMessages =  $cleanedMessages->makeHidden('received_messages');

            return response()->json([
                'status' => 'success',
                'data' => $cleanedMessages
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => "An error occurred while fetching users with last messages. $e",
            ], 500);
        }
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
