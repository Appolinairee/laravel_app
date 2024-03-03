<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Interaction\CommentStoreRequest;
use App\Models\Interaction;
use App\Models\Product;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    /**
     * Add a comment to a product.
     *
     * @param  \App\Models\Product  $product
     * @param  \App\Http\Requests\Interaction\CommentStoreRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function storeComment(Product $product, CommentStoreRequest $request)
    {
        try {
            $user = auth()->user();

            // check if user have paid the product
            $productsOrders = $user->orders->where('amount_paid', '>', 0)->filter(function ($order) use ($product) {
                return $order->order_items->contains(function ($orderItem) use ($product) {
                    return $orderItem->product_id === $product->id;
                });
            });

            if ($productsOrders->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
                ], 403);
            }

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
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    /**
     * Update comment.
     *
     * @param  \App\Http\Requests\Interaction\CommentStoreRequest  $request
     * @param  \App\Models\Interaction  $comment
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateComment(CommentStoreRequest $request, Interaction $comment)
    {
        try {
            $this->authorize('update', $comment);

            $comment->update([
                'content' => $request->input('content'),
            ]);

            $comment->load('user');

            return response()->json([
                'status' => 'success',
                'data' => $comment,
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }


    /**
     * Delete Comment.
     *
     * @param  \App\Models\Interaction  $comment
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteComment(Interaction $comment)
    {
        try {
            $this->authorize('update', $comment);

            $comment->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Commentaire supprimé avec succès.',
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas autorisé à faire cette action.',
            ], 403);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la suppression du commentaire.',
            ], 500);
        }
    }



    /**
     * Get comments for a specific product.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function getCommentsByProduct(Product $product, Request $request)
    {
        try {
            $perPage = $request->get('perPage', 4);
            $userId =  intval($request->get('userId'));

            $comments = $product->comments()->with('user')->orderBy('created_at', 'desc')
                ->paginate($perPage);

            if ($userId) {
                $comments->getCollection()->transform(function ($comment) use ($userId) {
                    $commentArray = $comment->toArray();
                    $commentArray['is_current_user'] = $comment->user_id == $userId;
                    return $commentArray;
                });
            }

            return response()->json([
                'status' => 'success',
                'data' => $comments->items(),
                'pagination' => [
                    'nextUrl' => $comments->nextPageUrl(),
                    'prevUrl' => $comments->previousPageUrl(),
                    'total' => $comments->total(),
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
