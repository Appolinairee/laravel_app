<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\Interaction\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RestoreAccountRequestRequest;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class RestoreUserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore($user_id)
    {
        try {
            $user = User::withTrashed()->find($user_id);

            if (!auth()->user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
                ], 403);
            }else if (!$user){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non trouvé.',
                ], 404);
            }

            $user->restore();

            return response()->json([
                'status' => 'success',
                'data' => "Le compte est restauré avec succès"
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }



    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restoreRequest(User $user, RestoreAccountRequestRequest $request)
    {
        try {

            $user = User::withTrashed()->where('email', $request->email)->first();

            if(!$user){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non trouvé.',
                ], 404);
            }else if(!$user->deleted_at){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Le compte est déjà actif..',
                ], 403);
            }


            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                // intern notification for admins 

                $data = [
                    'subject' => 'Demande de restauration de compte!',
                    'greeting' => $admin->name,
                    'message' => 'L\'utilisateur @'. $user->name . '@ a fait une demande de restauration de compte.',
                    'actionText' => "Voir l'utilisateur",
                    'actionUrl' => '',
                ];

                $admin->notify(new GeneralNotification($data));

                // intern notification for admins 
                $notificationData  = [
                    'title' => "Demande de restauration de compte!",
                    'content' => "L\'utilisateur @'. $user->name . '@ a fait une demande de restauration de compte.",
                    'user_id' => $admin->id,
                    'notifiable_id' => $user->id,
                    'notifiable_type' => \App\Models\User::class,
                    'status' => 0
                ];
    
                (new NotificationController)->store($notificationData);
            }

            return response()->json([
                'status' => 'success',
                'data' => "Votre requête est soumise aux administrateurs"
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
