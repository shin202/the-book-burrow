<?php

namespace App\Services;

use App\Enums\DiscountTypeEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    private const string DEFAULT_INSTANCE = 'default';
    private static self $cartInstance;
    private string $instance;

    private function __construct()
    {
        $this->instance = self::DEFAULT_INSTANCE . '-cart';
    }

    public static function instance(string $instance = null): self
    {
        $instance = $instance ?? self::DEFAULT_INSTANCE;

        if (!isset(self::$cartInstance) || self::$cartInstance->instance !== $instance . '-cart') {
            self::$cartInstance = new self;
            self::$cartInstance->instance = $instance . '-cart';
        }

        return self::$cartInstance;
    }

    public function add(mixed $id, string $name = null, int $quantity = null, array $attributes = null): CartItem
    {
        $cartItem = $this->createCartItem($id, $name, $quantity, $attributes);
        $items = $this->getItems();

        if ($items->has($cartItem->id)) {
            $cartItem->quantity += $items->get($cartItem->id)->quantity;
        }

        $items->put($cartItem->id, $cartItem);
        Session::put($this->instance, $items);

        return $cartItem;
    }

    private function createCartItem($id, $name, $quantity, $attributes): CartItem
    {
        $cartItem = CartItem::fromAttributes($id, $name, $attributes);
        $cartItem->setQuantity($quantity);

        return $cartItem;
    }

    protected function getItems(): Collection
    {
        return Session::get($this->instance) ?? new Collection;
    }

    public function get($cartItemId): ?CartItem
    {
        $items = $this->getItems();

        if (!$items->has($cartItemId)) {
            return null;
        }

        return $items->get($cartItemId);
    }

    public function update($cartItemId, $quantity): CartItem
    {
        $cartItem = $this->get($cartItemId);
        $cartItem->quantity = $quantity;

        $items = $this->getItems();

        if ($cartItem->quantity <= 0) {
            $this->remove($cartItem->id);
        } else {
            $items->put($cartItem->id, $cartItem);
        }

        Session::put($this->instance, $items);

        return $cartItem;
    }

    public function remove($cartItemId): void
    {
        $cartItem = $this->get($cartItemId);
        $items = $this->getItems();
        $items->pull($cartItem->id);
        Session::put($this->instance, $items);
    }

    public function destroy(): void
    {
        Session::remove($this->instance);
    }

    public function __get($property)
    {
        if ($property === 'total') return $this->total();
        if ($property === 'items') return $this->items();
        if ($property === 'count') return $this->count();
        if ($property === 'subtotal') return $this->subtotal();
        if ($property === 'tax') return $this->tax();
        if ($property === 'discount') return $this->discount();
        if ($property === 'newSubtotal') return $this->newSubtotal();

        return null;
    }

    public function total(): float
    {
        $items = $this->getItems();

        return $items->reduce(fn($total, CartItem $item) => $total + $item->total, 0);
    }

    public function items(): Collection
    {
        return Session::get($this->instance) ?? new Collection([]);
    }

    public function count(): int
    {
        $items = $this->getItems();

        return $items->sum(fn(CartItem $item) => $item->quantity);
    }

    public function subtotal()
    {
        $items = $this->getItems();

        return $items->reduce(fn($total, CartItem $item) => $total + $item->subtotal, 0);
    }

    public function tax()
    {
        $items = $this->getItems();

        return $items->reduce(fn($total, CartItem $item) => $total + $item->taxTotal, 0);
    }

    public function discount()
    {
        $items = $this->getItems();

        return $items->reduce(fn($total, CartItem $item) => $total + $item->discountTotal, 0);
    }

    public function newSubtotal()
    {
        $items = $this->getItems();

        return $items->reduce(fn($total, CartItem $item) => $total + $item->newSubtotal, 0);
    }

    public function setTax(string $id, int|float $taxRate): void
    {
        $cartItem = $this->get($id);

        $cartItem->setTaxRate($taxRate);

        $items = $this->getItems();

        $items->put($cartItem->id, $cartItem);

        Session::put($this->instance, $items);
    }

    public function clearDiscount(string $id): void
    {
        $cartItem = $this->get($id);

        $cartItem->setDiscount(0, DiscountTypeEnum::FIXED);

        $items = $this->getItems();

        $items->put($cartItem->id, $cartItem);

        Session::put($this->instance, $items);
    }

    public function setDiscount(string $id, int|float $discountValue, DiscountTypeEnum $discountType): void
    {
        $cartItem = $this->get($id);

        $cartItem->setDiscount($discountValue, $discountType);

        $items = $this->getItems();

        $items->put($cartItem->id, $cartItem);

        Session::put($this->instance, $items);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0 || !Session::has($this->instance);
    }
}
