<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Interaction;
use App\Models\Product;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    /**
     * Login user and generate new token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product)
    {
        try {

            $this->authorize('storeInteraction', $product);

            $request->validate([
                'content' => 'required|string',
            ]);

            $comment = Interaction::create([
                'type' => 'comment',
                'user_id' => auth()->id(),
                'entity_id' => $product->id,
                'entity_type' => Product::class,
                'content' => $request->input('content'),
            ]);

            $comment->load('user');

            return response()->json([
                'status' => 'success',
                'data' => $comment,
            ], 201);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
