<?php

namespace App\Http\Controllers\Api\Interaction;

use App\Http\Controllers\Controller;
use App\Models\Notification;
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
}
