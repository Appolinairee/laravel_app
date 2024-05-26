<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Exception;

class LoginController extends Controller
{
    /**
     * Login user and generate new token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(LoginRequest $request)
    {
        try {
            $identifiers = $request->only(['email', 'password']);

            if (auth()->attempt($identifiers)) {
                $user = auth()->user();
                if ($user->email_verified_at) {
                    $token = $user->createToken(env('BACKEND_KEY'))->plainTextToken;

                    $notificationCount = $user->notifications()->where('state', 0)->count();
                    $messageCount = $user->messages()->where('status', 0)->count();

                    if($user->creator)
                        $user->load('creator');

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Utilisateur connecté',
                        'data' => [
                            'user' => $user,
                            'token' => $token,
                            'notification_count' => $notificationCount,
                            'message_count' => $messageCount
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Échec de l\'authentification.',
                        'errors' => 'Veuillez confirmez votre adresse email. Un message vous a été envoyé.'
                    ], 404);
                }
            }

            // verify if user is deleted
            $user = User::withTrashed()->where('email', $identifiers['email'])->first();

            if ($user->deleted_at) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Authentification interdite',
                    'errors' => 'Le compte a été supprimé. Veuillez faire une requête de restauration.'
                ], 403);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Échec de l\'authentification',
                'errors' => 'Adresse Email ou mot de passe incorrect!'
            ], 404);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
