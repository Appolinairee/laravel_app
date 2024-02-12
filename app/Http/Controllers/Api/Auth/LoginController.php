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
            // verify identifiers 
            $identifiers = $request->only(['email', 'password']);

            if(auth()->attempt($identifiers)){
                $user = auth()->user();
                if($user->email_verified_at){
                    $token = $user->createToken(env('BACKEND_KEY'))->plainTextToken;
                
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Utilisateur connecté',
                        'data' => [
                            'user' => $user,
                            'token' => $token
                        ]
                    ], 200);
                }else{
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Échec de l\'authentification.',
                        'errors' => 'Adresse mail non encore vérifiée.'
                    ], 404);
                }

            }
            
            // verify if user is deleted
            $user = User::withTrashed()->where('email', $identifiers['email'])->first();

            if($user->deleted_at){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Authentification interdite',
                    'errors' => 'Le compte a été supprimé. L\'utilisateur doit prendre des mesures appropriées'
                ], 403);
            }
            
            return response()->json([
                'status' => 'error',
                'message' => 'Échec de l\'authentification',
                'errors' => 'Identifiants invalides'
            ], 404);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}