<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class StoreProductController extends Controller
{
    public function __invoke(StoreProductRequest $request){
        if(!auth()->user()->creator){
            
            $product = Product::create([
                'title' => $request->title,
                'caracteristics' => $request->caracteristics,
                'delivering' => $request->delivering,
                'old_price' => $request->old_price,
                'current_price' => $request->current_price,
            ]);

            // notifications for admins
            $admins = User::where('role', 'admin')->get();
    
            foreach ($admins as $admin) {
                $admin->notify(new NewCreatorNotification($creator->user->name));
            }

        }else {
            return response()->json([
                'status' => 'false',
                'message' => 'Vous êtes déjà créateur.',
            ], 403);
        }
    }
}
