<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Interaction;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class LikeContoller extends Controller
{
    /**
     * Add or remove Like fro product.
     *
     * @param  \App\Models\Product  $product
     * @param  \App\Http\Requests\Interaction\CommentStoreRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function storeLike(Product $product)
    {
        try {
            $user = auth()->user();

            $existingLike = Interaction::where('user_id', auth()->id())
                ->where('entity_id', $product->id)
                ->where('entity_type', Product::class)
                ->where('type', 'like')
                ->first();

            if($existingLike){
                $existingLike->forceDelete();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Annulation du like',
                    'data' => $existingLike,
                ], 201);
            }

            $like = Interaction::create([
                'type' => 'like',
                'user_id' => auth()->id(),
                'entity_id' => $product->id,
                    'entity_type' => Product::class,
            ]);

            $like->load('user');

            return response()->json([
                'status' => 'success',
                'message' => 'Like ajouté avec succès',
                'data' => $like,
            ], 201);

        } catch (Exception $e) {
            return response()->json($e);
        }
    }





    /**
     * Get likes for a specific product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function getLikesByProduct(Product $product, Request $request)
    {
        try {
            $perPage = $request->get('perPage', 4);
            $userId =  intval($request->get('userId'));

            $likes = $product->likes()->with('user')->orderBy('created_at', 'desc')->paginate($perPage);

            if ($userId) {
                $likes->getCollection()->transform(function ($like) use ($userId) {
                    $likeArray = $like->toArray();
                    $likeArray['is_current_user'] = $like->user_id == $userId;
                    return $likeArray;
                });
            }

            return response()->json([
                'status' => 'success',
                'data' => $likes->items(),
                'pagination' => [
                    'nextUrl' => $likes->nextPageUrl(),
                    'prevUrl' => $likes->previousPageUrl(),
                    'total' => $likes->total(),
                ],
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching comments.',
            ], 500);
        }
    }
}
