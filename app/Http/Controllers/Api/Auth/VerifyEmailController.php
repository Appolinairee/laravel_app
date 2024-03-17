<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VerifyEmailController extends Controller
{
    public function sendLink (Request $request){
        try {
            $data = $request->validate([
                'email' => 'required|email|exists:users,email'
            ]);

            $user = User::where('email', $data['email'])->first();

            Mail::to($data['email'])->send(new VerifyEmailMail($user->name, $data['email']));
        
            return response()->json([
                'status' => 'success',
                'message' => 'Un mail de vérification a été envoyé.',
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function verifyEmail(Request $request){
        try {
            if (!$request->hasValidSignature()) {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Lien de vérification non valide.',
                ], 400);
            }

            $user = User::where('email', $request->email)->first();

            if(!$user->email_verified_at){
                $user->forceFill([
                    'email_verified_at' => now()
                ])->save();

                $user->notify(new WelcomeNotification);

                Auth::loginUsingId($user['id']);

                if (Auth::check()) {
                    $token = $user->createToken(env('BACKEND_KEY'))->plainTextToken;
                    
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Utilisateur connecté',
                        'data' => [
                            'user' => $user,
                            'token' => $token
                        ]
                    ], 200);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Échec de l\'authentification',
                    ], 401);
                }

                // return response()->json([
                //     'status' => 'success',
                //     'message' => 'Le mail de l\'utilisateur est vérifié',
                // ], 200);
            }else {
                return response()->json([
                    'status' => 'failure',
                    'message' => 'Le mail de l\'utilisateur est déjà vérifié',
                ], 200);
            }

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}