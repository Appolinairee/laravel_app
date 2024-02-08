<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    /**
     * Handle the incoming request for user register.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __invoke(RegisterRequest $request)
    {
        try {

            // Vérifier si l'utilisateur existe dans la corbeille
            // $existingUser = User::onlyTrashed()
            //                 ->where('email', $request->email)
            //                 ->first();

            // if ($existingUser) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Un compte avec ces informations existe déjà. Veuillez restaurer votre compte précédent.',
            //     ], 400);
            // }


            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password, [
                    'rounds' => 12
                ]),
                'phone' => $request->phone,
                'location' => $request->location
            ]);

            $token = $user->createToken(env('BACKEND_KEY'))->plainTextToken;
            Mail::to($user->email)->send(new VerifyEmailMail($user));

            return response()->json([
                'status' => 'success',
                'message' => 'Utilisateur enrégistré. Un mail de vérification a envoyé',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
