<?php

namespace App\Policies;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrderPolicy
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
    public function view(User $user, Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Order $order): Response
    {
        return $user->isAdministrator() || $user->id === $order->user_id
            ? Response::allow()
            : Response::deny('You do not have permission to update this order.');
    }

    public function updateStatus(User $user, Order $order): Response
    {
        $excludedStatuses = [OrderStatusEnum::CANCELLED, OrderStatusEnum::DELIVERED, OrderStatusEnum::REFUNDED];

        return $user->isAdministrator() && !in_array($order->currentStatus->status, $excludedStatuses)
            ? Response::allow()
            : Response::deny('You do not have permission to update this order status.');
    }

    public function cancel(User $user, Order $order): Response
    {
        return ($user->isAdministrator() || $user->id === $order->user_id)
        && $order->currentStatus->status === OrderStatusEnum::PENDING
            ? Response::allow()
            : Response::deny('You do not have permission to cancel this order.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Order $order): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Order $order): bool
    {
        //
    }
}
