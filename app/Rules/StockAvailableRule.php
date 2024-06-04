<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class StockAvailableRule implements ValidationRule, DataAwareRule
{
    protected array $data = [];
    protected string $table;
    protected string $column;
    protected string $stockColumn;
    protected mixed $value;

    public function __construct($table, $column = 'id', $stockColumn = 'quantity_in_stock', $value = null)
    {
        $this->table = $table;
        $this->column = $column;
        $this->stockColumn = $stockColumn;
        $this->value = $value;
    }


    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $model = $this->table::query()->where($this->column, $this->value ?? $value)->first();

        if ($model->isOutOfStock()) {
            $fail("Sorry, $model->title is out of stock.");
        }

        if ($model->{$this->stockColumn} < $value) {
            $fail("Sorry, we just have {$model->{$this->stockColumn}} items left of $model->title.");
        }
    }

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
