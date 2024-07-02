<?php

namespace App\Http\Traits;

trait PermissionCheckTrait
{
    /**
     * Vérifie si l'utilisateur est un admin.
     *
     * @return bool
     */
    public function isAdmin()
    {
        return auth()->user()->isAdmin();
    }

    /**
     * Vérifie si l'utilisateur a créé l'entité spécifiée.
     *
     * @param mixed $entity
     * @return bool
     */
    public function createdByCurrentUser($entity)
    {
        return $entity->user_id === auth()->user()->id;
    }

    /**
     * Vérifie si l'utilisateur a les permissions nécessaires.
     *
     * @param mixed $entity
     * @return bool
     */
    public function hasPermission($entity)
    {
        return $this->isAdmin() || $this->createdByCurrentUser($entity);
    }
}
