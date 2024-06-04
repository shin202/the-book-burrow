<?php

namespace App\Services;

use App\Enums\DiscountTypeEnum;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class CartItem implements Arrayable
{
    /**
     * @var string
     */
    public string $id;

    /**
     * @var mixed
     */
    public mixed $productId;

    /**
     * @var int
     */
    public int $quantity;

    /**
     * @var string|null
     */
    public ?string $name = null;

    /**
     * @var array|null
     */
    public ?array $attributes;

    private $associatedModel = null;

    private int|float $discountValue = 0;

    private DiscountTypeEnum $discountType = DiscountTypeEnum::FIXED;

    private int|float $taxRate = 0;

    /**
     * @param string $productId
     * @param string|null $name
     * @param array|null $attributes
     */
    public function __construct(mixed $productId, string $name = null, array $attributes = null)
    {
        $this->productId = $productId;
        $this->name = $name;
        $this->attributes = $attributes;
        $this->id = $this->generateId($productId, $attributes);
    }

    private function generateId(mixed $id, array|null $attributes = null): string
    {
        $attributes = $attributes ?? [];
        ksort($attributes);

        return md5($id . serialize($attributes));
    }

    public static function fromArray(array $data): self
    {
        $attributes = Arr::get($data, 'attributes', []);

        return new self($data['productId'], $data['name'], $data['price'], $attributes);
    }

    public static function fromAttributes($id, $name, $attributes): self
    {
        return new self($id, $name, $attributes);
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function setDiscount(float $discountValue, DiscountTypeEnum $discountType): self
    {
        $this->discountValue = $discountValue;
        $this->discountType = $discountType;

        return $this;
    }

    public function setTaxRate(int|float $taxRate): self
    {
        $this->taxRate = $taxRate;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'productId' => $this->productId,
            'quantity' => $this->quantity,
            'name' => $this->name,
            'price' => $this->price,
            'attributes' => $this->attributes,
        ];
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->{$property};
        }

        if ($property === 'subtotal') {
            return $this->quantity * $this->getPrice();
        }

        if ($property === 'priceTax') {
            return $this->getPrice() + $this->tax;
        }

        if ($property === 'tax') {
            return $this->getPrice() * ($this->taxRate / 100);
        }

        if ($property === 'taxTotal') {
            return $this->tax * $this->quantity;
        }

        if ($property === 'discount') {
            return $this->discountType === DiscountTypeEnum::FIXED ?
                $this->discountValue :
                $this->getPrice() * ($this->discountValue / 100);
        }

        if ($property === 'discountTotal') {
            return $this->discount * $this->quantity;
        }

        if ($property === 'newSubtotal') {
            return $this->subtotal - $this->discountTotal;
        }

        if ($property === 'priceDiscount') {
            return $this->getPrice() - $this->discount;
        }

        if ($property === 'total') {
            return $this->newSubtotal + $this->tax_total;
        }

        if ($property === 'price') {
            return $this->getPrice();
        }

        if ($property === 'model' && isset($this->associatedModel)) {
            return once(function () {
                return app($this->associatedModel)->find($this->productId);
            });
        }

        return null;
    }

    private function getPrice()
    {
        return once(function () {
            return $this->model->discount_price ?? $this->model->price;
        });
    }

    public function associate($model)
    {
        $this->associatedModel = is_string($model) ? $model : get_class($model);

        return $this;
    }
}
