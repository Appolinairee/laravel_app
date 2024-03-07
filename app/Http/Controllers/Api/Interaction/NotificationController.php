<?php

namespace App\Http\Controllers\Api\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Store a newly created notification in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Models\Notification
     */
    public function store(array $data)
    {
        try {
            // Validate form data if necessary
            $notification = Notification::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'user_id' => $data['user_id'],
                'notifiable_id' => $data['notifiable_id'],
                'notifiable_type' => $data['notifiable_type'],
            ]);

            return $notification;
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function updateNotificationState(Notification $notification)
    {
        try {
            if (auth()->user()->id !== $notification->user_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
                ], 403);
            }

            $notification->update([
                'state' => 1
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Notification lue.',
                'data' => $notification,
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    public function getUserNotifications(Request $request)
    {
        try {
            $perPage = $request->get('perPage', 10);

            $notifications = auth()->user()->notifications()->with(['notificationEntity'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'message' => 'Notifications de l\'utilisateur.',
                'data' => $notifications->items(),
                'pagination' => [
                    'nextUrl' => $notifications->nextPageUrl(),
                    'prevUrl' => $notifications->previousPageUrl(),
                    'total' => $notifications->total(),
                ],
                'current_page' => $notifications->currentPage()
            ]);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
