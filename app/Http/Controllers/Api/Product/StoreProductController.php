<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Api\Interaction\NotificationController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Exception;
use Illuminate\Support\Str;


class StoreProductController extends Controller
{
    public function storeProduct(StoreProductRequest $request)
    {
        try {

            if (auth()->user()->isAdmin()) {
                $productData = [
                    'title' => $request->title,
                    'caracteristics' => $request->caracteristics,
                    'delivering' => null,
                    'old_price' => null,
                    'current_price' => 0,
                    'creator_id' => 0,
                    'disponibility' => 1,
                    'status' => 2,
                    'slug_name' => Str::slug($request->title),
                ];
        
        
                $product = Product::create($productData);
        
                return response()->json([
                    'status' => 'success',
                    'message' => 'Produit Atoun ajouté avec succès.',
                    'data' => $product
                ], 201);

            } else if (auth()->user()->creator) {
                $creator = auth()->user()->creator;

                // notifications for admins
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    // extern notification by mail
                    $data = [
                        'subject' => 'Un nouveau produit MIA!',
                        'greeting' => $admin->name,
                        'message' => 'Le créateur @' . $creator->name . '@ a créé un nouveau produit dénommé @' . $request->title . '@.',
                        'actionText' => 'Voir le produit pour confirmer',
                        'actionUrl' => '',
                    ];

                    $admin->notify(new GeneralNotification($data));
                }

                $productData = [
                    'title' => $request->title,
                    'caracteristics' => $request->caracteristics,
                    'delivering' => $request->delivering,
                    'old_price' => $request->old_price,
                    'current_price' => $request->current_price,
                    'creator_id' => $creator->id,
                    'disponibility' => $request->disponibility,
                    'slug_name' => Str::slug($request->title),
                ];

                // quantity can be defined when disponibility is 1(true)
                if (isset($request->quantity) && $request->disponibility == 1) {
                    $productData['quantity'] = $request->quantity;
                } else if (isset($request->quantity) && $request->disponibility != 1) {
                    return response()->json([
                        'status' => 'false',
                        'message' => 'Produit non disponible. Vous ne pouvez indiquer le nombre',
                    ], 403);
                }

                $product = Product::create($productData);

                if (!empty($request->category_ids)) {
                    $product->categories()->attach($request->category_ids);
                }

                if ($request->new_category) {
                    $slug = Str::slug($request->new_category);

                    Category::create([
                        'name' => $request->new_category,
                        'image' => null,
                        'slug' => $slug,
                        'statut' => 'inactive'
                    ]);
                }

                // intern notification for admins
                foreach ($admins as $admin) {

                    $notificationData  = [
                        'title' => "Un nouveau produit.",
                        'content' => "Le créateur $creator->name a publié un nouveau produit.",
                        'user_id' => $admin->id,
                        'notifiable_id' => $product->id,
                        'notifiable_type' => \App\Models\Product::class,
                        'status' => 0
                    ];

                    (new NotificationController)->store($notificationData);
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Votre produit a été ajouté avec succès. AtounAfrica s\'empresse de le confirmer.',
                    'data' => $product->fresh('categories')
                ], 201);
            } else {
                return response()->json([
                    'status' => 'false',
                    'message' => 'Vous n\'êtes pas encore un créateur.',
                ], 403);
            }
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    // private function storeAtounProduct($request)
    // {
    //     $productData = [
    //         'title' => $request->title,
    //         'caracteristics' => $request->caracteristics,
    //         'delivering' => null,
    //         'old_price' => null,
    //         'current_price' => 0,
    //         'creator_id' => 0,
    //         'disponibility' => 1,
    //     ];


    //     $product = Product::create($productData);

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Produit Atoun ajouté avec succès.',
    //         'data' => $product
    //     ], 201);
    // }
}
