<?php

namespace App\Http\Controllers\Api\Creator;

use App\Http\Controllers\Api\Interaction\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreCreatorRequest;
use App\Http\Traits\CrudActions;
use App\Models\Creator;
use App\Models\User;
use App\Notifications\NewCreatorNotification;

class StoreCreatorController extends Controller
{
    use CrudActions;

    /**
     * Handle the incoming request.
     *
     * @param  \App\Http\Requests\User\StoreCreatorRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(StoreCreatorRequest $request)
    {
        return $this->tryCatchWrapper(function () use ($request) {
            if (auth()->user()->creator) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous êtes déjà créateur.',
                ], 403);
            }

            $creator = $this->createCreator($request);

            $this->notifyAdminsAndCreator($creator);

            $user = auth()->user();
            $user->load('creator');

            return response()->json([
                'status' => 'success',
                'message' => 'Votre requête pour devenir créateur sur AtounAfrica est prise en compte.',
                'data' => $user,
            ], 201);
        });
    }

    /**
     * Create creator and corresponding wallet.
     *
     * @param  \App\Http\Requests\User\StoreCreatorRequest  $request
     * @return \App\Models\Creator
     */
    private function createCreator(StoreCreatorRequest $request)
    {
        $logoPath = $request->hasFile('logo')
            ? $request->file('logo')->store('logos', 'public')
            : null;


        $creator = Creator::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'logo' => $logoPath,
            'status' => 0,
            'description' => $request->description,
            'location' => $request->location,
            'delivery_options' => $request->delivery_options,
            'payment_options' => $request->payment_options,
            'user_id' => auth()->user()->id,
            'payment_method' => $request->payment_method,
        ]);

        $creator->user->wallets()->create([
            'balance' => 0,
            'wallet_type' => 'creator',
        ]);

        return $creator;
    }

    /**
     * Notify admin for creator creation.
     *
     * @param  \App\Models\Creator  $creator
     * @return void
     */
    private function notifyAdminsAndCreator(Creator $creator)
    {
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewCreatorNotification($creator->name));
        }

        $notificationData = [
            'title' => 'Demande acceptée.',
            'content' => 'Nous répondons généralement dans les heures qui suivent.',
            'user_id' => $creator->user->id,
            'notifiable_id' => $creator->id,
            'notifiable_type' => Creator::class,
        ];
        (new NotificationController)->store($notificationData);
    }
}
