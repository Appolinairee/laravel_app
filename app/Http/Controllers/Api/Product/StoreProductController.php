<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Models\Product;
use App\Models\User;
use App\Notifications\GeneralNotification;
use App\Notifications\NewCreatorNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreProductController extends Controller
{
    public function __invoke(StoreProductRequest $request){
        if(auth()->user()->creator){
            $creator = auth()->user()->creator;

            $product = Product::create([
                'title' => $request->title,
                'caracteristics' => $request->caracteristics,
                'delivering' => $request->delivering,
                'old_price' => $request->old_price,
                'current_price' => $request->current_price,
                'creator_id' => $creator->id,
            ]);

            // notifications for admins
            $admins = User::where('role', 'admin')->get();
    
            foreach ($admins as $admin) {
                $data = [
                    'subject' => 'Un nouveau produit MIA!',
                    'greeting' => $admin->name,
                    'message' => 'Le créateur @'. $creator->name .'@ a créé un nouveau produit dénommé @'. $product->title . '@.',
                    'actionText' => 'Voir le produit pour confirmer',
                    'actionUrl' => '',
                ];

                $admin->notify(new GeneralNotification($data));
            }


            return response()->json([
                'status' => 'success',
                'message' => 'Votre produit a été ajouté avec succès. AtounAfrica s\'empresse de le confirmer.',
                'data' => $creator
            ], 201);

        }else {
            return response()->json([
                'status' => 'false',
                'message' => 'Vous n\'êtes pas encore un créateur.',
            ], 403);
        }
    }
}
