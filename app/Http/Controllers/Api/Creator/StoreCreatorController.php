<?php

namespace App\Http\Controllers\Api\Creator;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreCreatorRequest;
use App\Models\Creator;
use App\Models\User;
use App\Notifications\NewCreatorNotification;
use Exception;

class StoreCreatorController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(StoreCreatorRequest $request)
    {
        try {
            
            $creator = Creator::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'logo' => $request->logo,
                'description' => $request->description,
                'location' => $request->location,
                'delivery_options' => $request->delivery_options,
                'payment_options' =>  $request->payment_options,
                'user_id' => auth()->user()->id
            ]);

            // notification for admins
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewCreatorNotification($creator->user->name));
            }


            return response()->json([
                'status' => 'success',
                'message' => 'Votre requête pour devenir créteur sur AtounAfrica est pris en compte.',
                'data' => $creator
            ], 201);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
