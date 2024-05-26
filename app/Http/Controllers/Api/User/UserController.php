<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateUserRequest;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Get User by Id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function get($userId = null)
    {

        try {
            if ($userId == auth()->user()->id || auth()->user()->isAdmin()) {
                $user = User::find($userId);

                if($user->creator)
                    $user->load('creator');

                if ($user) {
                    return response()->json([
                        'status' => 'success',
                        'data' => $user,
                    ], 201);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Utilisateur non trouvé',
                    ], 404);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    /**
     * Get auth user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function authUser()
    {

        try {
            $user = auth()->user();

            if ($user) {

                $user->notification_count = $user->notifications()->where('state', 0)->count();
                $user->message_count = $user->messages()->where('status', 0)->count();

                if($user->creator)
                    $user->load('creator');

                return response()->json([
                    'status' => 'success',
                    'data' => $user,
                ], 201);
                
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Utilisateur non trouvé',
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    /**
     * update User.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        try {
            if (auth()->user()->id !== $user->id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de mettre à jour ce profil.',
                ], 403);
            } else {

                $userData = $request->only(['name', 'email', 'phone', 'location']);
                $message = "";
                $oldEmail = $user->email;

                if (empty($userData)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Aucune information à mettre à jour.',
                    ], 400);
                }

                $user->update($userData);

                // vérification si le mail est à changer
                if ($request->has('email') && $user->email !== $oldEmail) {
                    $message = $this->updateEmail($user);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Mise à jour du profil effectuée.' . $message,
                    'data' => $user,
                ], 201);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function delete(User $user)
    {
        try {
            if (auth()->user()->id !== $user->id && !$this->authorize('isAdmin', auth()->user())) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de supprimer cet utilisateur.',
                ], 403);
            } else {

                // solf delete
                $user->delete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Suppression effectuée avec succès.'
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    private function updateEmail($user)
    {
        $user->update([
            'email_verified_at' => null,
        ]);

        // envoie de mail
        Mail::to($user->email)->send(new VerifyEmailMail($user));
        Auth::user()->tokens()->delete();

        $mailIsSendMessage = " Changement d'adresse mail. Un mail a été envoyé. L'utilisateur a été déconnecté.";
        return $mailIsSendMessage;
    }


    public function getUsersInTrash(Request $request)
    {
        try {
            $perPage = $request->get('perPage', 15);

            $users = User::onlyTrashed()->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'current_page' => $users->currentPage(),
                'data' => $users->items(),
                'pagination' => [
                    'nextUrl' => $users->nextPageUrl(),
                    'prevUrl' => $users->previousPageUrl(),
                    'total' => $users->total(),
                ],
            ], 200);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
