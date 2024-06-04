<?php

namespace App\Policies;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BannerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Banner $banner): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->isAdministrator() ?
            Response::allow() :
            Response::deny('You are not authorized to create a banner');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Banner $banner): Response
    {
        return $user->isAdministrator() ?
            Response::allow() :
            Response::deny('You are not authorized to update this banner');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Banner $banner): Response
    {
        return $user->isAdministrator() ?
            Response::allow() :
            Response::deny('You are not authorized to delete this banner');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Banner $banner): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Banner $banner): bool
    {
        //
    }
}
