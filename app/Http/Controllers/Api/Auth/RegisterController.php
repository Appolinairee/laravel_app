<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Traits\CrudActions;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    use CrudActions;

    /**
     * Handle the incoming request for user register.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function __invoke(RegisterRequest $request)
    {
        return $this->tryCatchWrapper(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password, [
                    'rounds' => 12
                ]),
                'phone' => $request->phone,
                'location' => $request->location,
                'affiliate_code' => substr(md5($request->name), 0, 8)
            ]);

            $user->wallets()->create([
                'balance' => 0,
                'wallet_type' => 'user',
            ]);

            Mail::to($request->email)->send(new VerifyEmailMail($request->name, $request->email));

            return response()->json([
                'status' => 'success',
                'message' => 'Utilisateur enregistré. Un email de vérification a été envoyé.',
            ], 201);
        });
    }
}
