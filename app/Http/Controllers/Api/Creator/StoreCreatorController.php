<?php

namespace App\Http\Controllers\Api\Creator;

use App\Http\Controllers\Api\Interaction\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreCreatorRequest;
use App\Models\Creator;
use App\Models\User;
use App\Notifications\NewCreatorNotification;
use Exception;
use Illuminate\Support\Facades\Storage;

class StoreCreatorController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(StoreCreatorRequest $request)
    {
        try {

            if (!auth()->user()->creator) {

                if ($request->hasFile('logo')) {
                    $logoPath = $request->file('logo')->store('logos', 'public');
                } else {
                    $logoPath = null;
                }

                $admins = User::where('role', 'admin')->get();

                foreach ($admins as $admin) {
                    $admin->notify(new NewCreatorNotification($request->name));
                }

                $creator = Creator::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                    'logo' => $logoPath,
                    'status' => 0,
                    'description' => $request->description,
                    'location' => $request->location,
                    'delivery_options' => $request->delivery_options,
                    'payment_options' =>  $request->payment_options,
                    'user_id' => auth()->user()->id
                ]);

                // send notifications 
                $this->notifyAdminsAndCreator($admins, $creator);
                $user = auth()->user();
                $user->load('creator');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Votre requête pour devenir créateur sur AtounAfrica est pris en compte.',
                    'data' =>  $user 
                ], 201);
                
            } else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Vous êtes déjà créateur.',
                ], 403);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    private function notifyAdminsAndCreator($admins, $creator)
    {

        foreach ($admins as $admin) {
            $notificationData  = [
                'title' => "Un nouveau créateur MIA.",
                'content' => "Demande de création de compte vendeur par $creator->name.",
                'user_id' => $admin->id,
                'notifiable_id' => $creator->id,
                'notifiable_type' => \App\Models\Creator::class,
            ];

            (new NotificationController)->store($notificationData);
        }

        $notificationData  = [
            'title' => "Demande acceptée.",
            'content' => "Nous répondons généralement dans les heures qui suivent.",
            'user_id' => $creator->user->id,
            'notifiable_id' => $creator->id,
            'notifiable_type' => \App\Models\Creator::class,
        ];

        (new NotificationController)->store($notificationData);

    }
}
