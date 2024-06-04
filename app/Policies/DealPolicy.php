<?php

namespace App\Policies;

use App\Models\DailyDeal;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DealPolicy
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
    public function view(User $user, DailyDeal $dailyDeal): bool
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
            Response::deny('You are not authorized to create a deal.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DailyDeal $dailyDeal): Response
    {
        return $user->isAdministrator() ?
            Response::allow() :
            Response::deny('You are not authorized to update this deal.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DailyDeal $dailyDeal): Response
    {
        return $user->isAdministrator() ?
            Response::allow() :
            Response::deny('You are not authorized to delete this deal.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DailyDeal $dailyDeal): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DailyDeal $dailyDeal): bool
    {
        //
    }
}
