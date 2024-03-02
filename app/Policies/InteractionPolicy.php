<?php

namespace App\Policies;

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
        return true;
    }
}
