<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Number;

class CouponPolicy
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
    public function view(User $user, Coupon $coupon): bool
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
            Response::deny('You are not authorized to create a coupons.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Coupon $coupon): Response
    {
        return $user->isAdministrator() ?
            Response::allow() :
            Response::deny('You are not authorized to update this coupons.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Coupon $coupon): Response
    {
        return $user->isAdministrator() ?
            Response::allow() :
            Response::deny('You are not authorized to delete this coupons.');
    }

    public function apply(User $user, Coupon $coupon): Response
    {
        $numUsed = $user->coupons()
            ->where('coupon_id', $coupon->id)
            ->count();

        if ($numUsed >= $coupon->usage_per_user) {
            return Response::deny('You have used up the maximum number of times for this coupons.');
        }

        if ($coupon->isExpired() || !$coupon->isAvailable()) {
            return Response::deny('The coupons is expired or has reached the maximum number of uses.');
        }

        if (CartService::instance()->total() < $coupon->minimum_order_amount) {
            return Response::deny('Your order does not meet the minimum amount required for this coupons. (Minimum: ' . Number::currency($coupon->minimum_order_amount) . ')');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Coupon $coupon): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Coupon $coupon): bool
    {
        //
    }
}
