<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordLinkRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Mail\Auth\ResetPassswordMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function sendLink(ResetPasswordLinkRequest $request){
        try {
            Mail::to($request->email)->send(new ResetPassswordMail($request->email));

            return response()->json([
                'status' => 'success',
                'message' => 'Lien de réinitialisation envoyé',
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function reset(ResetPasswordRequest $request){
        $user = User::whereEmail($request->email)->first();

        try {
            if(!$user){
                return response()->json([
                    'status' => 'failure',
                    'message' => 'L\utilisateur est introuvable'
                ], 404);
            }
    
            $user->password = Hash::make($request->password, [
                'rounds' => 12
            ]);
            $user->save();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Mot de passe mise à jour avec succès'
            ], );
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


}
