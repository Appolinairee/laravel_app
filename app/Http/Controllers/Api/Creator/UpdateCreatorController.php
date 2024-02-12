<?php

namespace App\Http\Controllers\Api\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateCreatorRequest;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\Creator;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UpdateCreatorController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UpdateCreatorRequest $request, Creator $creator)
    {
        try {
            if (auth()->user()->id !== $creator->id && !auth()->user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de mettre à jour ce créateur.',
                ], 403);
            }else{
                
                $creatorData = $request->only(['name', 'phone', 'email', 'logo', 'description', 'location', 'delivery_poptions', 'payment_options']);

                $message = "";
                $oldEmail = $creator->email;

                if (empty($creatorData)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Aucune information à mettre à jour.',
                    ], 400);
                }

                $creator->update($creatorData);

                // vérification si le mail est à changer
                if ($request->has('email') && $creator->email !== $oldEmail) {
                    $message = $this->updateEmail($creator);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Mise à jour du profil effectuée.' . $message,
                    'data' => $creator,
                ], 200);
                
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    private function updateEmail($creator){
        $creator->update([
            'email_verified_at' => null,
        ]);

        // envoie de mail
        Mail::to($creator->email)->send(new VerifyEmailMail($creator));
        Auth::user()->tokens()->delete();
        
        $mailIsSendMessage = "Changement d'adresse mail. Un mail a été envoyé. L'utilisateur a été déconnecté.";
        return $mailIsSendMessage;
    }
}
