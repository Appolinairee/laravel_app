<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Logout user by delecting token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        try {
            Auth::user()->tokens()->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Utilisateur déconnecté avec succès'
            ], 200);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
