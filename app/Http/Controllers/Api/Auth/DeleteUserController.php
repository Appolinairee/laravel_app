<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class DeleteUserController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(User $user)
    {
        try {
            if($user->id !== auth()->id() && !auth()->user()->isAdmin()){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
                ], 403);
            }

            $user->delete();

            return response()->json([
                'status' => 'success',
                'data' => "Le coimpte a été supprimé avec succès"
            ], 200);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
