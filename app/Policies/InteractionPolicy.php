<?php

namespace App\Policies;

use App\Models\Interaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InteractionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can store the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function storeInteraction(User $user, Product $product)
    {
        return $user->orders->where('amount_paid', '>', 0)->filter(function ($order) use ($product) {
            return $order->order_items->contains(function ($orderItem) use ($product) {
                return $orderItem->product_id === $product->id;
            });
        })->isNotEmpty();
    }


    /**
     * Determine whether the user can store the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Interaction  $comment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Interaction  $comment)
    {
        return $user->isAdmin() || $comment->user_id == $user->id;
    }
}
