<?php

namespace App\Http\Controllers\Api\Creator;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateCreatorRequest;
use App\Mail\Auth\VerifyEmailMail;
use App\Models\Creator;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

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
            if (auth()->user()->id !== $creator->user_id && !auth()->user()->isAdmin()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'avez pas l\'autorisation de mettre à jour ce créateur.',
                ], 403);
            }else{
                
                $creatorData = $request->only(['name', 'phone', 'email', 'description', 'location', 'delivery_options', 'payment_options', 'payment_method']);

                if (empty($creatorData) && !$request->hasFile('logo')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => $creatorData,
                    ], 400);
                }

                if($request->hasFile('logo')){
                    //delete old logo
                    Storage::delete($creator->logo);

                    // store new logo
                    $logoPath = $request->file('logo')->store('logos', 'public');
                    $creator->update(['logo' => $logoPath]);
                }

                $message = "";
                $oldEmail = $creator->email;

                $creator->update($creatorData);

                // verify if mail is updated
                if ($request->has('email') && $creator->email !== $oldEmail) {
                    $message = $this->updateEmail($creator);
                }

                $user = auth()->user();
                $user->load('creator');

                return response()->json([
                    'status' => 'success',
                    'message' => 'Mise à jour du profil effectuée.' . $message,
                    'data' => $user,
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
        Mail::to($creator->email)->send(new VerifyEmailMail($creator->name, $creator->email));
        Auth::user()->tokens()->delete();
        
        $mailIsSendMessage = "Changement d'adresse mail. Un mail a été envoyé. L'utilisateur a été déconnecté.";
        return $mailIsSendMessage;
    }
}
